import tds1_telemetry as tel

coordinates = tel.get_current_position("TDS1.tle")

print str(coordinates.latitude) + "," + str(coordinates.longitude)