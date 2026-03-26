#include <WiFi.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>
#include <Keypad.h>

// ==========================
// Wi-Fi credentials
// ==========================
const char* ssid = "bc";
const char* password = "clusters123";

// ==========================
// MQTT broker settings
// ==========================
const char* mqttServer = "broker.hivemq.com";
const int mqttPort = 1883;
const char* mqttUser = "";
const char* mqttPassword = "";

const char* buzzerTopic = "nit/buzzer/001";
const char* keypadTopic = "nit/keypad/24";
WiFiClient espClient;
PubSubClient client(espClient);

const int buzzerPin = 25;
bool buzzerState = false;
const byte ROWS = 4; 
const byte COLS = 4; 
char keys[ROWS][COLS] = {
  {'1','2','3','A'},
  {'4','5','6','B'},
  {'7','8','9','C'},
  {'*','0','#','D'}
};
byte rowPins[ROWS] = {14, 25, 33, 35};
byte colPins[COLS] = {26, 27, 34, 32};

Keypad keypad = Keypad(makeKeymap(keys), rowPins, colPins, ROWS, COLS);
void connectWiFi() {
  Serial.print("Connecting to Wi-Fi");
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println();
  Serial.println("Wi-Fi connected");
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
}

void playTone() {
  ledcWriteTone(buzzerPin, 1000);
  delay(300);
  ledcWriteTone(buzzerPin, 0);
  delay(50);
}

void mqttCallback(char* topic, byte* payload, unsigned int length) {
  Serial.println();
  Serial.print("Message received on topic: ");
  Serial.println(topic);

  char message[length + 1];
  for (unsigned int i = 0; i < length; i++) {
    message[i] = (char)payload[i];
  }
  message[length] = '\0';

  Serial.print("Raw payload: ");
  Serial.println(message);

  JsonDocument doc;
  DeserializationError error = deserializeJson(doc, message);

  if (error) {
    Serial.print("JSON parse failed: ");
    Serial.println(error.c_str());
    return;
  }

  if (strcmp(topic, buzzerTopic) == 0) {
    const char* state = doc["state"] | "";
    Serial.print("received buzzer state: ");
    Serial.println(state);

    if (strcmp(state, "on") == 0) {
      buzzerState = true;
      Serial.println("Buzzer turned ON");
    } 
    else if (strcmp(state, "off") == 0) {
      buzzerState = false;
      Serial.println("Buzzer turned OFF");
    }
  }
}
void connectMQTT() {
  while (!client.connected()) {
    Serial.print("Connecting to MQTT...");
    String clientId = "ESP32Client-";
    clientId += String((uint32_t)ESP.getEfuseMac(), HEX);

    bool connected;
    if (strlen(mqttUser) > 0) {
      connected = client.connect(clientId.c_str(), mqttUser, mqttPassword);
    } else {
      connected = client.connect(clientId.c_str());
    }

    if (connected) {
      Serial.println("connected");
      client.subscribe(buzzerTopic);
      Serial.print("Subscribed to: ");
      Serial.println(buzzerTopic);
    } else {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      Serial.println(" retrying in 3 seconds");
      delay(3000);
    }
  }
}
void publishKeypad(char key) {
  JsonDocument doc;
  doc["key"] = String(key);
  char buffer[100];
  serializeJson(doc, buffer);

  client.publish(keypadTopic, buffer);
  Serial.print("Published keypad key: ");
  Serial.println(key);
}
void setup() {
  Serial.begin(115200);
  ledcAttach(buzzerPin, 2000, 8);

  connectWiFi();
  client.setServer(mqttServer, mqttPort);
  client.setCallback(mqttCallback);
}
void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("Wi-Fi lost. Reconnecting...");
    connectWiFi();
  }

  if (!client.connected()) {
    connectMQTT();
  }

  client.loop();

  if (buzzerState) {
    playTone();
  }
  char key = keypad.getKey();
  if (key) {
    publishKeypad(key); 
  }
}
