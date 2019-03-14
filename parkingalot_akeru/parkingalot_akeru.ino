#include <Akeru.h>
#include "serialcom.h"

#define TX 4
#define RX 5
#define N_SPACES 17
#define N_BYTES 3

#define STR 0xF0
#define END 0xFF 

Akeru akeru(RX, TX);

bool space_img[N_SPACES];
bool image_available[N_SPACES];
bool sensor[N_SPACES];
bool sensor_available[N_SPACES];
bool space[N_SPACES];
byte buf[N_BYTES+2];

/*
 *
 *
 *
 *
 *
*/
void byte_to_bool(byte * buf, bool * b){
  int s=0;
  
  for(int i=0;i<N_BYTES;i++){
    for(int j=0;j<7;j++){
      if((buf[i+1] & (1 << (6-j)))==0){
        b[s] = 0;
      }else{
        b[s] = 1;
      }

      s++;
  
      if(s == N_SPACES){
        return;
      } 
    }
  }

  return;
}


/*
 *
 *
 *
 *
 *
*/
void setup() {
  start_serial();

  akeru.begin();
  akeru.echoOff();
}

/*
 *
 *
 *
 *
 *
*/
void loop() {

  if(receive_serial(buf, N_BYTES)){
    byte_to_bool(buf,space_img);
    
    Serial.println();
    for(int i=0; i<N_SPACES; i++){
      Serial.print(space_img[i]);
    }
    Serial.println();
  
  }

  

  

  //check_changes();
  
  unknown_timed_request();
  
  //String msg = akeru.toHex(sensor);

  //if(akeru.sendPayload(msg)){
    //Serial.println("Message sent!");
  //}else{
    //Serial.println("Error: Message not sent!");
  //}

}
