#! /bin/bash
TEMPCPU=$(sensors | grep 'temp2' | awk '{print $2}' | cut -b2,3,4,5)

echo "{\"temps\" : { \"cpu\" : \"$TEMPCPU\"  }}"
