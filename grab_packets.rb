#!/usr/bin/env ruby

require 'rubygems'
# this line imports the libpcap ruby bindings
require 'pcaplet'


# create a sniffer that grabs the first 1500 bytes of each packet
$network = Pcaplet.new('-s 80')

# create a filter that uses our query string and the sniffer we just made
$filter = Pcap::Filter.new('udp and dst port 53', $network.capture)

# add the new filter to the sniffer
$network.add_filter($filter)
ts_now = Time.now.to_i
collect_ts = ts_now.to_i + 3
#puts "collect until - " + collect_ts.to_s

# iterate over every packet that goes through the sniffer

for p in $network
  # print packet data for each packet that matches the filter
  puts p.ip_src
  break if Time.now.to_i >= collect_ts
end
