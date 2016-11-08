#! /bin/bash
# This script pulls the raw number as a string from lm_sensors' sensors command for CPU temps
# it then puts the value into json which then can be used elsewhere

# 'temp2' is unique to my setup, you may need to change this to match the temp you want to poll for.
#  technically you can poll for more than one value here just by duping the line below and changing the grep part
TEMPCPU=$(sensors | grep 'temp2' | awk '{print $2}' | cut -b2,3,4,5)

echo "{\"temps\" : { \"cpu\" : \"$TEMPCPU\"  }}"
