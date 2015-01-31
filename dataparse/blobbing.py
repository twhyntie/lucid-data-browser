# Algorithm for non-continuous blobbing

from lucidreader import LucidFile
import numpy as np
import math

class BlobFinder:

	def square(self, x, y, size):
		half_size = (size - 1) / 2
		x, y  = x - half_size, y - half_size
		# Return a sizexsize square of pixels around the coordinates
		pixels = []
		for i in range(size):
			for j in range(size):
				if (x + i < 0 or y + j < 0) or (x + i > 255 or y + j > 255):
					continue # Can't have out of bounds coordinates
				else:
					pixels.append((x + i, y + j))
		return pixels

	def add(self, x, y):
		self.frame[x][y] = 0 # Pixel has already been processed so can be set to 0
		close_region = self.square(x, y, self.SQUARE_SIZE)
		for pixel in close_region:
			if self.frame[pixel[0]][pixel[1]] > 0:
				self.blob.append((pixel[0], pixel[1]))
				self.add(pixel[0], pixel[1])


	def find_blobs(self):
		blobs = []
		self.blob = None # Holds currently active blob

		for x in range(256):
			for y in range(256):
				active_pixel = self.frame[x][y]
				if active_pixel > 0:
					# Create new blob
					self.blob = [(x, y)]
					
					self.add(x, y)

					self.blob = Blob(self.blob)
					blobs.append(self.blob)
					self.frame[x][y] = 0

		return blobs

	def write_blob_file(self, filename):
		file_obj = open(filename, 'w')
		for blob in self.find_blobs():
			file_obj.write(str(blob.pixel_list) + "\n")
		file_obj.close()

	def __init__(self, frame, search_radius):

		self.SQUARE_SIZE = search_radius
		self.frame = frame

# Class for storing blobs, and calculating their attributes
class Blob:

	def __init__(self, pixels):
		self.pixel_list = pixels
		# Calculate centroid
		x_values, y_values = [], []
		for pixel in pixels:
			x_values.append(pixel[0])
			y_values.append(pixel[1])
		self.centroid = ( float(sum(x_values)) / len(x_values) , float(sum(y_values)) / len(y_values) )
		
		# Calculate radius
		self.radius = 0
		for pixel in pixels:
			x_distance = abs(pixel[0] - self.centroid[0])
			y_distance = abs(pixel[1] - self.centroid[1])
			distance = math.hypot(x_distance, y_distance)
			if distance > self.radius:
				self.radius = distance
		self.radius += 0.5 # Stop 1 particle tracks having a radius of 0
		self.radius = math.ceil(self.radius)

	def relativise(self):
		new_blob = []
		min_x, min_y = 256, 256
		for pixel in self.pixel_list:
			if pixel[0] < min_x:
				min_x = pixel[0]
			if pixel[1] < min_y:
				min_y = pixel[1]
		for pixel in self.pixel_list:
			new_blob.append(((pixel[0] - min_x) + 1, (pixel[1] - min_y) + 1))
		self.pixel_list = new_blob
