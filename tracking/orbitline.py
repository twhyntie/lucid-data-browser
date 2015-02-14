import tds1_telemetry as tel 
import time
import sys

ts = int(time.time())

ts = ts - (50 * 60)

for i in range(100):
	pos = tel.get_position("/lucid/telemetry/TDS1.txt", ts)
	sys.stdout.write(str(pos.latitude) + "," + str(pos.longitude))
	if i < 99:
		sys.stdout.write(";")
	ts += 60

print
