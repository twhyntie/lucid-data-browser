from lucidreader import LucidFile
import frameplot
import tds1_telemetry as tel
import os
import Image
import sys, os
import blobbing

default_folder = False
incoming_folder = "../ldat/incoming/"

if len(sys.argv) == 1:
	files = os.listdir("../ldat/incoming")
	default_folder = True
else:
	print "Reading from folder " + sys.argv[1]
	files = os.listdir(sys.argv[1])
	incoming_folder = sys.argv[1] + "/"

for filename in files:
	print "Processing: " + filename

	datafile = LucidFile(incoming_folder + filename, 3)

	# Extract date and time from filename 
	fields = filename.split("_")
	f_date = fields[3]
	f_time = fields[4]
	dt = f_date[0:4] + "-" + f_date[4:6] + "-" + f_date[6:8] + " " + f_time[0:2] + "." + f_time[2:4]
	rootfolder = "/var/www/lucid-data-browser/data/" + dt + "/" # Always remember ending slash!!

	num_frames = datafile.num_frames - 1

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
			xycfile = open(framefolder + "c" + str(channel) + ".xyc", "w")
			for x in range(256):
				for y in range(256):
					c_val = frame[x][y]
					c_val = c_val / 256.0
					c_val *= 11810
					xycfile.write(str(x) + "\t" + str(y) + "\t" + str(c_val) + "\n")				
			xycfile.close()
			# Find blobs in frame
			#blobfinder = blobbing.BlobFinder(frame, 9) # Seems to be the best search radius for non-continuous LUCID data
			#blobfile = open(framefolder + "c" + str(channel) + ".clusters", "w")
			#blobs = blobfinder.find_blobs()
			#for blob in blobs:
			#	blobfile.write(str(int(blob.centroid[0])) + " " + str(int(blob.centroid[1])) + " " + str(int(blob.radius)) + "\n")
			#blobfile.close()

		pos = tel.get_position("TDS1.tle", data.timestamp)
		meta += str(i + 1) + " " + str(data.timestamp) + " " + str(pos.latitude) + " " + str(pos.longitude) + "\n"

	# Write meta file 

	f = open(rootfolder + "metadata", "w")
	f.write(meta[:-1]) # trim off ending newline
	f.close()
	print "done"

if default_folder:
	os.system("mv ../ldat/incoming/* ../ldat/done") # move all files to 'done' folder
