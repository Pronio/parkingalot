#include <Akeru.h>
#include <Wire.h>

#define TX 4
#define RX 5
#define SensorAddr 0x48
#define TempReg 0x00

Akeru akeru(RX, TX);

int sensor;

void setup() {
  Serial.begin(9600);
  Wire.begin();

  if(!akeru.begin())
  {
    Serial.println("Error: Modem failed to startup");
    while(1);
  }

  akeru.echoOff();
}

void loop() {

  Wire.beginTransmission(SensorAddr);
  Wire.write(TempReg);
  Wire.endTransmission();

  Wire.requestFrom(SensorAddr,1);

  if(Wire.available()==1){
    sensor = Wire.read();
  }
  
  Serial.print("Temp = ");
  Serial.println(sensor);
  
  String msg = akeru.toHex(sensor);

  if(akeru.sendPayload(msg)){
    Serial.println("Message sent!");
  }else{
    Serial.println("Error: Message not sent!");
  }

  while(1);
}
