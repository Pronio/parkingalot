import serial
import time
import struct

port = "/dev/ttyUSB1"

s1=serial.Serial(port,9600, parity=serial.PARITY_NONE,
            stopbits=serial.STOPBITS_ONE,
            bytesize=serial.EIGHTBITS,
            dsrdtr=False,
            rtscts=False,
            timeout=1)

s1.reset_input_buffer()
s1.reset_output_buffer()
s1.setDTR(False)
s1.setRTS(False)

while 1:
	s1.write(struct.pack('>B',0x31))
	if s1.in_waiting>0:
		print(s1.read(1))
	time.sleep(1)