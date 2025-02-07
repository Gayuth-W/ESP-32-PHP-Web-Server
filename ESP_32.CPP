#include <TinyGPS++.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <stdbool.h>

const char* ssid     = "***";
const char* password = "***";

const char* SERVER_NAME = "***";
String ESP32_API_KEY = "***";

unsigned long previousMillis = 0;
long interval = 30000;

#define rxPin 16
#define txPin 17
HardwareSerial neogps(1);
TinyGPSPlus gps;

void setup() {
  Serial.begin(115200);
  Serial.println("Initializing ESP32...");

  neogps.begin(9600, SERIAL_8N1, rxPin, txPin);

  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    reconnectWiFi();
  }

  unsigned long currentMillis = millis();
  if (currentMillis - previousMillis >= interval) {
    previousMillis = currentMillis;
    sendGpsToServer();
  }

  delay(1000);
}

void reconnectWiFi() {
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
}

void sendGpsToServer() {
  bool newData = false;

  for (unsigned long start = millis(); millis() - start < 2000;) {
    while (neogps.available()) {
      if (gps.encode(neogps.read())) {
        if (gps.location.isValid()) {
          newData = true;
          break;
        }
      }
    }
  }

  if (newData) {
    String latitude = String(gps.location.lat(), 6);
    String longitude = String(gps.location.lng(), 6);

    Serial.print("Latitude: ");
    Serial.print(latitude);
    Serial.print(", Longitude: ");
    Serial.println(longitude);

    HTTPClient http;
    http.begin(SERVER_NAME);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    String gps_data = "api_key=" + ESP32_API_KEY;
    gps_data += "&lat=" + latitude;
    gps_data += "&lng=" + longitude;

    Serial.print("Sending data: ");
    Serial.println(gps_data);

    int httpResponseCode = http.POST(gps_data);

    if (httpResponseCode > 0) {
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
      String response = http.getString();
      Serial.println("Response: " + response);
    } else {
      Serial.print("HTTP request failed, error: ");
      Serial.println(httpResponseCode);
    }

    http.end();
  }
}
