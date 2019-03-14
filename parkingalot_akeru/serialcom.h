#include <Arduino.h>

#define TIME_UNKNOWN 30000

#define STR 0xF0
#define END 0xFF

int receive_serial(byte * buf, int buflen);
void unknown_timed_request();
void start_serial();
