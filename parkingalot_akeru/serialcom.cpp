#include "serialcom.h"
#include <Arduino.h>

bool unknown=1;
unsigned long timer_unknown = 0;

void start_serial(){
  Serial.begin(9600);
}


/*
 *
 *
 *
 *
 *
*/
int receive_serial(byte * buf, int buflen){
  int len=0;

  if(Serial.available()){
    len = Serial.readBytes(buf,1);

    if((len == 1) && (buf[0] == STR)){
      len = Serial.readBytesUntil(END,buf+1,buflen+1);

      if((len == buflen+1) && (buf[buflen+1] == END)){
        
        Serial.write(buf,buflen+2);
        unknown = 0;
        return 1;
        
      }else{
        Serial.write(STR);
        timer_unknown = millis();
        unknown = 1;
        return 0;
      }
    }else{
      Serial.write(STR);
      timer_unknown = millis();
      unknown = 1;
      return 0;
    }
  }
  return 0;  
}

/*
 *
 *
 *
 *
 *
*/
void unknown_timed_request(){
  if((unknown == 1) && ((unsigned long)(millis() - timer_unknown) > TIME_UNKNOWN)){
    Serial.write(STR);
    timer_unknown = millis();
  }
}

