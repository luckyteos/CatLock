/*
  TinyCircuits TinyScreen/TinyScreen+ Basic Example
  
  This example shows the basic functionality of the TinyScreen library,
  including drawing, writing bitmaps, and printing text
  
  Written 26 January 2016
  by Ben Rose
  Written 26 January 2016
  By Ben Rose
  Modified 20 May 2019
  By Hunter Hykes
  
  https://TinyCircuits.com
*/

#include <Wire.h>
#include <SPI.h>
#include <TinyScreen.h>
#include <STBLE.h>

//Debug output adds extra flash and memory requirements!
#ifndef BLE_DEBUG
#define BLE_DEBUG true
#endif

#if defined (ARDUINO_ARCH_AVR)
#define SerialMonitorInterface Serial
#elif defined(ARDUINO_ARCH_SAMD)
#define SerialMonitorInterface SerialUSB
#endif


uint8_t ble_rx_buffer[21];
char *accessCode = "None";
uint8_t ble_rx_buffer_len = 0;
uint8_t ble_connection_state = false;
#define PIPE_UART_OVER_BTLE_UART_TX_TX 0

//Library must be passed the board type
//TinyScreenDefault for TinyScreen shields
//TinyScreenAlternate for alternate address TinyScreen shields
//TinyScreenPlus for TinyScreen+
TinyScreen display = TinyScreen(TinyScreenDefault);

void setup(void) {
  SerialMonitorInterface.begin(9600);
  BLEsetup();
  Wire.begin();//initialize I2C before we can initialize TinyScreen- not needed for TinyScreen+
  display.begin();
  //setBrightness(brightness);//sets main current level, valid levels are 0-15
  display.setBrightness(10);
}

void loop() {
  /*hardwareDrawCommands();
  drawPixels();
  drawBitmap();
  //setFlip(boolean);//done in hardware on the SSD1331
  display.setFlip(true);
  delay(1000);
  display.setFlip(false);
  delay(1000);*/
  aci_loop();//Process any ACI commands or events from the NRF8001- main BLE handler, must run often. Keep main loop short.
  if (ble_rx_buffer_len) {//Check if data is available
    if (ble_rx_buffer_len == 4) {
      accessCode = (char*)ble_rx_buffer + '\0';
    }
  }
  screenController();
  /*delay(1000);
  readInput();*/
}

void screenController(){
  writeUnlockScreen();
  if (display.getButtons(TSButtonLowerLeft)){
    writePinScreen();
  } else if (display.getButtons(TSButtonLowerRight)){
    writeScanScreen();
  }
}

void writeScanScreen() {
  display.clearScreen();
  display.setFont(liberationSans_10ptFontInfo);
  int width=display.getPrintWidth("Scanning...");
  display.setCursor(51-(width/2),10);
  display.fontColor(TS_16b_White, TS_16b_Black);
  display.print("Scanning");
  for (int i = 0; i < 10; i++) {
    drawCircle(50,45,10,TS_8b_Blue);
    drawCircle(50,45,15,TS_8b_Yellow);
    delay(300);
    drawCircle(50,45,10,TS_8b_Black);
    drawCircle(50,45,15,TS_8b_Black);
    delay(300);
    drawCircle(50,45,10,TS_8b_Blue);
    drawCircle(50,45,15,TS_8b_Yellow);
    delay(300);
  }
  display.clearScreen();
}

void writeUnlockScreen(){
  //setFont sets a font info header from font.h
  //information for generating new fonts is included in font.h
  display.setFont(liberationSans_10ptFontInfo);
  //getPrintWidth(character array);//get the pixel print width of a string
  int width=display.getPrintWidth("Confirm Unlock?");
  //setCursor(x,y);//set text cursor position to (x,y)- in this example, the example string is centered
  display.setCursor(51-(width/2),10);
  //fontColor(text color, background color);//sets text and background color
  display.fontColor(TS_16b_White, TS_16b_Black);
  display.print("Confirm Unlock");
  display.setCursor(0, 43);
  display.fontColor(TS_8b_DarkGreen, TS_8b_Black);
  display.print("<- Yes");
  display.setCursor(64, 43);
  display.fontColor(TS_8b_Red, TS_8b_Black);
  display.print("No ->");
}

void writePinScreen() {
  display.clearScreen();
  display.fontColor(TS_16b_White, TS_16b_Black);
  int pinTxtWidth = display.getPrintWidth("PIN:");
  display.setCursor(38, 10);
  display.print("PIN:");
  display.setFont(liberationSans_14ptFontInfo);
  display.fontColor(TS_16b_Green, TS_16b_Black);
  display.setCursor(28, 30);
  display.print(accessCode);
  delay(5000);
  display.clearScreen();
}

void drawCircle(int x0, int y0, int radius, uint8_t color)
{
  int x = radius;
  int y = 0;
  int radiusError = 1-x;
 
  while(x >= y)
  {
    //drawPixel(x,y,color);//set pixel (x,y) to specified color. This is slow because we need to send commands setting the x and y, then send the pixel data.
    display.drawPixel(x + x0, y + y0, color);
    display.drawPixel(y + x0, x + y0, color);
    display.drawPixel(-x + x0, y + y0, color);
    display.drawPixel(-y + x0, x + y0, color);
    display.drawPixel(-x + x0, -y + y0, color);
    display.drawPixel(-y + x0, -x + y0, color);
    display.drawPixel(x + x0, -y + y0, color);
    display.drawPixel(y + x0, -x + y0, color);
    y++;
    if (radiusError<0)
    {
      radiusError += 2 * y + 1;
    }
    else
    {
      x--;
      radiusError += 2 * (y - x) + 1;
    }
  }
}
