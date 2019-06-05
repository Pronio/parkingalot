#include <Akeru.h>
#include "serialcom.h"

#define TX 4
#define RX 5
#define N_SPACES 17
#define N_BYTES 3
#define N_BYTES_SIG 3

#define STR 0xF0
#define END 0xFF 
enum condition {both,one_of,sensor,image};

Akeru akeru(RX, TX);

enum condition free_condition=image;
bool space_img[N_SPACES] = {0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0};
bool space_sensor[N_SPACES] = {0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0};
bool sensor_available[N_SPACES] = {0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0};
bool space[N_SPACES] = {0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0};
byte buf[N_BYTES+2];
byte buf_sig[N_BYTES_SIG];
unsigned long downlink_interval=1;

unsigned long timer_downlink = 0;




/*
 *
 *
 *
 *
 *
*/
int hex_to_int(char in){
  if(((int)in)>57){
    return ((int)in)-87;
  }else{
    return ((int)in)-48;
  }
}

/*
 *
 *
 *
 *
 *
*/
void downlink_request(){
  String data = "";
  
  if((unsigned long)(millis() - timer_downlink) > (downlink_interval*60000)){

    bool_to_byte(buf_sig, space);
    data = akeru.toHex(buf_sig,N_BYTES_SIG);
  
    if (akeru.receive(&data))
    {
      Serial.println(data);
      if(data[2]=='0'){
        free_condition=both;  
      }else if(data[2]=='1'){
        free_condition=one_of; 
      }else if(data[2]=='2'){
        free_condition=sensor;
      }else{
        free_condition=image;
      }

      downlink_interval=hex_to_int(data[3])*4096+hex_to_int(data[4])*256+hex_to_int(data[5])*16+hex_to_int(data[6]); 
      Serial.println(downlink_interval);
      Serial.println(free_condition);     

    }
  
    timer_downlink = millis();
  }
}



/*
 *
 *
 *
 *
 *
*/
int check_changes(){
  int change=0;

  for(int i=0; i < N_SPACES; i++){
    switch(free_condition){
      case both:
  
        if(sensor_available[i]){
          if(space[i]!=(space_img[i] & space_sensor[i])){
            space[i] = space_img[i] & space_sensor[i];
            change=1;
          }
        }else{
          if(space[i]!=space_img[i]){
            space[i] = space_img[i];
            change=1;
          } 
        }
        
        break;
      case one_of:

        if(sensor_available[i]){
          if(space[i]!=(space_img[i] | space_sensor[i])){
            space[i] = space_img[i] | space_sensor[i];
            change=1;
          }
        }else{
          if(space[i]!=space_img[i]){
            space[i] = space_img[i];
            change=1;
          }
        }
  
        break;
      case sensor:

        if(sensor_available[i]){
          if(space[i]!=space_sensor[i]){
            space[i] = space_sensor[i];
            change=1;
          }
        }else{
          if(space[i]!=space_img[i]){
            space[i] = space_img[i];
            change=1;
          }
        }
  
        break;
      case image:
        if(space[i]!=space_img[i]){
          space[i] = space_img[i]; 
          change=1;
        }

        break;
    }
  }

  return change; 
}



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
void bool_to_byte(byte * buf2, bool * b){
  int s=0;

  for(int i=0; i<N_BYTES_SIG; i++){
    buf2[i] = 0;
  }
  
  for(int i=0;i<N_BYTES_SIG;i++){
    for(int j=0;j<8;j++){
      if((i==0) && (j==0)){
        buf2[i] = 0x80;
      }else{
        buf2[i] = buf2[i] | (b[s] << (7 - j));
        s++;
      }
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
    
    /*Serial.println("img");
    for(int i=0; i<N_SPACES; i++){
      Serial.print(space_img[i]);
    }
    Serial.println();*/
  
  }

  
  downlink_request();
  

  if(check_changes()){
    
    Serial.println("space");
    for(int i=0; i<N_SPACES; i++){
      Serial.print(space[i]);
    }
    Serial.println();

    bool_to_byte(buf_sig, space);
    //Serial.write(buf_sig,N_BYTES_SIG);
    akeru.sendPayload(akeru.toHex(buf_sig,N_BYTES_SIG));
    
  }
  
  unknown_timed_request();

}
