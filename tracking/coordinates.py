import tds1_telemetry as tel

coordinates = tel.get_current_position("/lucid/telemetry/TDS1.txt")

print str(coordinates.latitude) + "," + str(coordinates.longitude)
