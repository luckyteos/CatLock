/*SecLocker 1.01*/

// This library is for the wifi connection
#include <Wire.h>
#include <SPI.h>
#include <WiFi101.h>
#include <TinyScreen.h>
#include <DHT.h>;
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
//Need dtostrf from avr library to do proper float/double to string conversion
#include <avr/dtostrf.h>
#include "arduino_secrets.h"

/*********************** EDIT THIS SECTION TO MATCH YOUR INFO *************************/
char ssid[] = SECRET_SSID;  //  your network SSID (name)
char wifiPassword[] = SECRET_PASS;  // your network password
#define DHTPIN 3     // what pin we're connected to
#define DHTTYPE DHT22   // DHT 22  (AM2302)

// Define Serial object based on which TinyCircuits processor board is used.
#if defined (ARDUINO_ARCH_AVR)
  #define SerialMonitorInterface Serial
#elif defined(ARDUINO_ARCH_SAMD)
  #define SerialMonitorInterface SerialUSB
#endif

int chk;
float temp; //Stores temperature value
int status = WL_IDLE_STATUS;
char server[] = SERVER_IP;
char lockStatus[10] = "Locked";
double currentTemp;
double thresholdTemp = 80.0;
WiFiClient client;

unsigned long lastConnectionTime = 0;            // last time you connected to the server, in milliseconds
const unsigned long postingInterval = 1L * 1000L; // delay between updates, in milliseconds (30 seconds => milliseconds)

TinyScreen display = TinyScreen(TinyScreenDefault);
DHT dht(DHTPIN, DHTTYPE);


void setup() {
  SerialMonitorInterface.begin(9600);
  Wire.begin();//initialize I2C before we can initialize TinyScreen- not needed for TinyScreen+
  display.begin();
  //sets main current level, valid levels are 0-15
  display.setBrightness(10);
  WiFi.setPins(8, 2, A3, -1); // VERY IMPORTANT FOR TINYDUINO
  dht.begin();
  
  if (WiFi.status() == WL_NO_SHIELD) {
    Serial.println("Wifi Shield not present");
    while(true);
  }

  while (status != WL_CONNECTED){
    display_connection();
    SerialMonitorInterface.print("Attempting to connect to SSID: ");
    SerialMonitorInterface.println(ssid);
    status = WiFi.begin(ssid, wifiPassword);
    // wait 10 seconds for connection:
    delay(10000);
  }
  
  // Print out the local IP address
  SerialMonitorInterface.println("");
  SerialMonitorInterface.println("WiFi connected");
  printWiFiStatus();

  SerialMonitorInterface.println("\n Starting connection to server...");
}

void loop()
{
  char respLine[50] = "";
  double recvThreshold = 0;
  char thresholdStr[6] = "";

  while(client.available()){
    char c = client.read();
    get_current_temp();
    
    
    if (c == '\n') {
       if (strlen(respLine) != 0){
          respLine[0] = '\0';
       }
    } else if (c != '\r'){
       appendChar(respLine, c);
    }

    //Result array to store temperature or lock status
    char result[10] = "";
    if (strlen(respLine) == 15){
      // Logic to parse the temperature properly
      if (startsWith("Threshold:", respLine)){
        if (respLine[10] == '+'){
          if (respLine[11] == 'x') {
            strncpy(result, respLine+12, 3);
          } else {
            strncpy(result, respLine+11, 4);
          }
        } else if (respLine[10] == '-'){
          if (respLine[11] == 'x') {
            result[0] = '-';
            char buff[8] = "";
            strncpy(buff, respLine+12, 3);
            mystrcat(result, buff);
          } else {
            strncpy(result, respLine+10, 5);
          }
        }

        result[strlen(result)] = '\0';
        recvThreshold = atof(result);

        SerialMonitorInterface.print("Current Temp: ");
        SerialMonitorInterface.print(currentTemp);
        SerialMonitorInterface.println(" Celsius");
        SerialMonitorInterface.print("Current Threshold: ");
        SerialMonitorInterface.println(thresholdTemp);
        

        if (thresholdTemp != recvThreshold) {
            thresholdTemp = recvThreshold;
        }

        SerialMonitorInterface.print("New Threshold: ");
        SerialMonitorInterface.println(thresholdTemp);
        
        //SerialMonitorInterface.println(result);
      }
    } else if (strlen(respLine) == 13){
      if (startsWith("Lock:", respLine)){
          strncpy(result, respLine+5, 8);
          result[strlen(result)] = '\0';
          //SerialMonitorInterface.println(result);

          if (strcmp(result, "Unlocked") == 0) {
             SerialMonitorInterface.println("Unlock Lock");
             lockStatus[0] = '\0';
             strncpy(lockStatus, "Unlocked", 9);
             writeUnlock();
             SerialMonitorInterface.println(lockStatus);
          }
      }
    } else if (strlen(respLine) == 11){
      if (startsWith("Lock:", respLine)){
          strncpy(result, respLine+5, 6);
          result[strlen(result)] = '\0';
          //SerialMonitorInterface.println(result);

          if (strcmp(result, "Locked") == 0) {
             SerialMonitorInterface.println("Lock lock");
             lockStatus[0] = '\0';
             strncpy(lockStatus, "Locked", 7);
             writeLock();
             SerialMonitorInterface.println(lockStatus);
          }
      }
    }
  }

  if (currentTemp > thresholdTemp){
    //Unlock Lock
    if(strcmp(lockStatus, "Unlocked") != 0){
      lockStatus[0] = '\0';
      strncpy(lockStatus, "Unlocked", 9);
    }
  }

  if (millis() - lastConnectionTime > postingInterval) {
    update_device_status();
  }
}

void update_device_status() {
  // close any connection before send a new request.
  // This will free the socket on the WiFi shield
  client.stop();
  
  if (client.connect(server, 80)){
    SerialMonitorInterface.println("Connected to server");
    char tempStr[6] = "";
    char thresholdStr[6] = "";
    char contentLen[3] = "";
    char lenHeader[20] = "Content-Length: ";
    char bodyText[100] = "";

    //Convert length of request body into a string for appending
    itoa(makeRequestBody(bodyText), contentLen, 10);
    mystrcat(lenHeader, contentLen);
    
    //HTTP Headers
    client.println("POST /device_status HTTP/1.1");
    client.print("Host: ");
    client.println(server);
    client.println(lenHeader);
    client.println("Content-Type: text/plain");
    client.println("User-Agent: ArduinoWiFi/1.1");
    client.println("Connection: close");
    client.println();
    //HTTP Body containing data to be sent to server
    client.println(bodyText);
    client.println();
    
    // note the time that the connection was made:
    lastConnectionTime = millis();
  } else {
    SerialMonitorInterface.println("Connection Failed");
  }
}

int makeRequestBody(char * bodyStr){
    char tempStr[6] = "";
    char thresholdStr[6] = "";
    
    processTempString(currentTemp, tempStr);
    processTempString(thresholdTemp, thresholdStr);

    mystrcat(bodyStr, "{\"Lock Status\":\"");
    mystrcat(bodyStr, lockStatus);
    mystrcat(bodyStr, "\",");
    mystrcat(bodyStr, "\"Temperature\":\"");
    mystrcat(bodyStr, tempStr);
    mystrcat(bodyStr, "\",");
    mystrcat(bodyStr, "\"Threshold\":\"");
    mystrcat(bodyStr, thresholdStr);
    mystrcat(bodyStr, "\"}");

    return strlen(bodyStr);
}

void processTempString(double tempType, char * tempStr) {
    if (tempType < 0){
       if (tempType > -10){
          dtostrf(tempType, 4, 1, tempStr);
       } else{
          dtostrf(tempType, 5, 1, tempStr);
       }
    } else if (tempType < 10) {
       dtostrf(tempType, 3, 1, tempStr);
    } else {
       dtostrf(tempType, 4, 1, tempStr);
    }
}

void get_current_temp(){
    currentTemp = dht.readTemperature();
}

void printWiFiStatus() {
  // print the SSID of the network you're attached to:
  SerialMonitorInterface.print("SSID: ");
  SerialMonitorInterface.println(WiFi.SSID());

  // print your WiFi shield's IP address:
  IPAddress ip = WiFi.localIP();
  SerialMonitorInterface.print("IP Address: ");
  SerialMonitorInterface.println(ip);

  // print the received signal strength:
  long rssi = WiFi.RSSI();
  SerialMonitorInterface.print("signal strength (RSSI):");
  SerialMonitorInterface.print(rssi);
  SerialMonitorInterface.println(" dBm");
}


void screenController(){
  if (strcmp(lockStatus, "Locked") == 0){
    writeLock();
  }
  else if (strcmp(lockStatus, "Unlocked") == 0){
    writeUnlock();
  }
}

//writes text on tinyscreen
void writeUnlock(){
  //setFont sets a font info header from font.h
  //information for generating new fonts is included in font.h
  display.clearScreen();
  display.setFont(thinPixel7_10ptFontInfo);
  //get the pixel print width of a string
  int width=display.getPrintWidth("Unlocked");
  //set text cursor position to (x,y)
  display.setCursor(48-(width/2),10);
  //draw lock
  display.drawLine(30,30,50,50,TS_8b_Green);
  display.drawLine(50,50,80,20,TS_8b_Green);
  //sets text and background color
  display.fontColor(TS_8b_Green,TS_8b_Black);
  display.print("Unlocked");
}

void writeLock(){
  //setFont sets a font info header from font.h
  //information for generating new fonts is included in font.h
  display.clearScreen();
  display.setFont(thinPixel7_10ptFontInfo);
  //get the pixel print width of a string
  int width=display.getPrintWidth("Locked");
  //set text cursor position to (x,y)
  display.setCursor(48-(width/2),10);
  //draw lock
  display.drawLine(0,60,100,30,TS_8b_Red);
  display.drawLine(0,30,100,60,TS_8b_Red);
  display.fontColor(TS_8b_Red,TS_8b_Black);
  display.print("Locked");
}

void display_connection(){
  //setFont sets a font info header from font.h
  //information for generating new fonts is included in font.h
  display.clearScreen();
  display.setFont(thinPixel7_10ptFontInfo);
  //get the pixel print width of a string
  int width=display.getPrintWidth("Connecting to Wifi...");
  display.setCursor(48-(width/2),10);
  display.print("Connecting to Wifi...");
}

// Credits to Fantastic Mr Fox for his answer from the stack overflow post at link: https://stackoverflow.com/questions/34055713/how-to-add-a-char-int-to-an-char-array-in-c
void appendChar(char *s, char c) {
  int len = strlen(s);
  s[len] = c;
  s[len+1] = '\0';
}

// Credits to Joshua Taylor for his answer from the stack overflow post at link: https://stackoverflow.com/questions/21880730/c-what-is-the-best-and-fastest-way-to-concatenate-strings
char* mystrcat( char* dest, char* src )
{
     while (*dest) dest++;
     while (*dest++ = *src++);
     return --dest;
}

//Credits to T.J. Crowder for his answer from the stack overflow post at link: https://stackoverflow.com/questions/4770985/how-to-check-if-a-string-starts-with-another-string-in-c
bool startsWith(const char *pre, const char *str)
{
    size_t lenpre = strlen(pre),
           lenstr = strlen(str);
    return lenstr < lenpre ? false : memcmp(pre, str, lenpre) == 0;
}
