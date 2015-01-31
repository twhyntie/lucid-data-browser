from lucidreader import LucidFile
import frameplot
import tds1_telemetry as tel
import os
import Image
import sys, os
import blobbing

files = os.listdir("../ldat/incoming")
for filename in files:
	print "Processing: " + filename

	datafile = LucidFile("../ldat/incoming/" + filename, 3)
	rootfolder = "../data/" + filename[:-5] + "/" # Always remember ending slash!!
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
			# Find blobs in frame
			blobfinder = blobbing.BlobFinder(frame, 9) # Seems to be the best search radius for non-continuous LUCID data
			blobfile = open(framefolder + "c" + str(channel) + ".clusters", "w")
			blobs = blobfinder.find_blobs()
			for blob in blobs:
				blobfile.write(str(int(blob.centroid[0])) + " " + str(int(blob.centroid[1])) + " " + str(int(blob.radius)) + "\n")
			blobfile.close()
			blobfile.write_blob_file(framefolder + "c" + str(channel) + ".blobfile")

		pos = tel.get_position("TDS1.tle", data.timestamp)
		meta += str(i + 1) + " " + str(data.timestamp) + " " + str(pos.latitude) + " " + str(pos.longitude) + "\n"

	# Write meta file 

	f = open(rootfolder + "metadata", "w")
	f.write(meta[:-1]) # trim off ending newline
	f.close()
	print "done"

os.system("mv ../ldat/incoming/* ../ldat/done") # move all files to 'done' folder
