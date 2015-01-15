from lucidreader import LucidFile
import frameplot
import tds1_telemetry as tel
import os
import Image

datafile = LucidFile("../ldat/" + raw_input("Enter data filename: "))
rootfolder = "../data/" + raw_input("Enter web name: ") + "/"
num_frames = int(raw_input("Enter number of frames(" + str(datafile.num_frames - 1) + "): "))

meta = str(num_frames) + " none\n"

for i in range(num_frames):
	print "processing frame", str(i + 1)
	os.makedirs(rootfolder + "frame" + str(i + 1))

	framefolder = rootfolder + "frame" + str(i + 1) + "/"

	data = datafile.get_frame(i)

	for channel in [0, 1, 3]:
		frame = data.channels[channel]
		image = frameplot.get_image(frame, "RGB")
		image.save(framefolder + "c" + str(channel) + ".png")

	pos = tel.get_position("TDS1.tle", data.timestamp)
	meta += str(i + 1) + " " + str(data.timestamp) + " " + str(pos.latitude) + " " + str(pos.longitude) + "\n"

# Write meta file 

f = open(rootfolder + "metadata", "w")
f.write(meta[:-1]) # trim off ending newline
f.close()
print "done"