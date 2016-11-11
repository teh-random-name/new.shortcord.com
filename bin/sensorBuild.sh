#! /bin/bash
# This script pulls the raw number as a string from lm_sensors' sensors command for CPU temps
# it then puts the value into json which then can be used elsewhere

# 'temp2' is unique to my setup, you may need to change this to match the temp you want to poll for.
#  technically you can poll for more than one value here just by duping the line below and changing the grep part

# cut is just used to pre-truncate the float, it's not /really/ needed

TEMPCPU=$(sensors -u | grep 'temp2_input' | awk '{print $2}' | cut -b1,2,3,4) # thermal diode
EXITFANSPEED=$(sensors -u | grep 'fan1_input' | awk '{print $2}' | cut -b1,2,3,4) # water block exhaust fan
PUMPFANSPEED=$(sensors -u | grep 'fan2_input' | awk '{print $2}' | cut -b1,2,3,4) # water block pump 

echo "{ \"temps\" : { \"cpu\" : \"$TEMPCPU\" }, \"fans\" : { \"exit\" : \"$EXITFANSPEED\", \"pump\" : \"$PUMPFANSPEED\" } }"
