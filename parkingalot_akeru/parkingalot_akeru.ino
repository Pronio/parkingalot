#include <Akeru.h>

#define TX 4
#define RX 5
#define N_SPACES 17
#define N_BYTES 3
#define TIME_UNKNOWN 30000

#define STR 0xF0
#define END 0xFF 

Akeru akeru(RX, TX);

bool space_img[N_SPACES];
bool image_available[N_SPACES];
bool sensor[N_SPACES];
bool sensor_available[N_SPACES];
bool space[N_SPACES];
bool unknown=1;
byte buf[N_BYTES+2];
int len=0, s=0;
unsigned long timer_unknown = 0;

/*
 *
 *
 *
 *
 *
*/
int receive_serial(byte * buf, int buflen){

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

void setup() {
  Serial.begin(9600);

  akeru.begin();
  akeru.echoOff();
}

void loop() {

  if(receive_serial(buf, N_BYTES)){
    for(int i=0;i<N_BYTES;i++){
      for(int j=0;j<7;j++){
        if((buf[i+1] & (1 << (6-j)))==0){
          space_img[s] = 0;
        }else{
          space_img[s] = 1;
        }
  
        if(s == N_SPACES-1){
          break;
        } 
        
        s++;
      }
  
      if(s == N_SPACES-1){
        s=0;
        break;
      } 
    }
      Serial.println();
    for(int i=0; i<N_SPACES; i++){
      Serial.print(space_img[i]);

    }
    Serial.println();
  }

  

  

  //check_changes();
  
  if((unknown == 1) && ((unsigned long)(millis() - timer_unknown) > TIME_UNKNOWN)){
    Serial.write(STR);
    timer_unknown = millis();
  }
  
  //String msg = akeru.toHex(sensor);

  //if(akeru.sendPayload(msg)){
    //Serial.println("Message sent!");
  //}else{
    //Serial.println("Error: Message not sent!");
  //}

}
