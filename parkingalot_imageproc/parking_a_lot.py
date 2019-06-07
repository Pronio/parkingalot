from imgproc import *

my_camera=Camera(960,720)

my_image = my_camera.grabImage()

# open a view setting the view to the size of the captured image
my_view = Viewer(my_image.width, my_image.height, "Parking a lot")

# display the image on the screen
my_view.displayImage(my_image)


#limite da folha

x_folha = [285,670,370,600]
y_folha = [705,705,452,452]

#Lugares de estacionamento

x_pos = [350,360,370,375,380,385,390,580,585,590,595,600,605,605,610,610,615]
y_pos = [585,560,535,515,495,475,458,458,475,495,515,535,560,585,615,645,685]


#first iteration
empty = [[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0],[0,0,0]]
counter = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
prev = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
space = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]

#analise de lugares de estacionamento
	
for i in range(17):

	x=x_pos[i]
	y=y_pos[i]

	value=[0,0,0];

	for j in range(10):
		for k in range(10):

			value[0]=value[0]+my_image[x+j,y+k][0]
			value[1]=value[1]+my_image[x+j,y+k][1]
			value[2]=value[2]+my_image[x+j,y+k][2]
	
	value[0]=value[0]/100
	value[1]=value[1]/100
	value[2]=value[2]/100

	empty[i][0]=value[0]
	empty[i][1]=value[1]
	empty[i][2]=value[2]





while 1:

	my_image = my_camera.grabImage()

	cur= [[0,255,0],[0,255,0],[0,255,0],[0,255,0],[0,255,0],[0,255,0],[0,255,0],[0,255,0],[0,255,0],[0,255,0],[0,255,0],[0,255,0],[0,255,0],[0,255,0],[0,255,0],[0,255,0],[0,255,0]]
	state = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]


	#analise de lugares de estacionamento
		
	for i in range(17):
	
		x=x_pos[i]
		y=y_pos[i]
	
		value=[0,0,0];
	
		for j in range(10):
			for k in range(10):
	
				value[0]=value[0]+my_image[x+j,y+k][0]
				value[1]=value[1]+my_image[x+j,y+k][1]
				value[2]=value[2]+my_image[x+j,y+k][2]

		value[0]=value[0]/100
		value[1]=value[1]/100
		value[2]=value[2]/100
		
		if (abs(value[0]-empty[i][0])+abs(value[1]-empty[i][1])+abs(value[2]-empty[i][2]))>100:
	
			cur[i][0]=255
			cur[i][1]=0
			state[i]=1



	#marcacao de extremos
	
	for i_f in range(4):
	
		x_f=x_folha[i_f]
		y_f=y_folha[i_f]
	
		for j_f in range(10):
			for k_f in range(10):
	
				my_image[x_f+j_f,y_f+k_f]=0,0,250
	


	#marcacao de lugares de estacionamento
	
	for i in range(17):
	
		x=x_pos[i]
		y=y_pos[i]
	
		for j in range(10):
			for k in range(10):
	
				my_image[x+j,y+k]=cur[i][0],cur[i][1],0
	
	# display the image on the screen
	my_view.displayImage(my_image)


	#processar mudancas

	change=0
	for i in range(17):
		if counter[i]==0:
			if prev[i]!=state[i]: 
				counter[i]=1
		elif counter[i]<4:
			if prev[i]!=state[i]:
				counter[i]=0
			else:
				counter[i]=counter[i]+1
		else:
			if prev[i]==state[i]:
				space[i]=state[i]
				change=1
			
			counter[i]=0

	prev=state

	#Agir na mudanca
	if change==1:
		for i in state:
			print(i)

		print("\n")
	

	
	# wait for 5 seconds, so we can see the changes
	waitTime(2000)