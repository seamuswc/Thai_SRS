require 'json'

def find_duplicates(array)
  unique_items = []
  duplicates = []

  array.each do |item|
    if unique_items.any? { |i| i['word'] == item['word'] }
      duplicates << item
    else
      unique_items << item
    end
  end

=begin 
  # Ensure all items with the same word are moved to duplicates
  unique_items.each do |unique_item|
    if duplicates.any? { |i| i['word'] == unique_item['word'] }
      duplicates << unique_item
    end
  end

  unique_items.reject! { |item| duplicates.any? { |i| i['word'] == item['word'] } }
=end

  { unique_items: unique_items, duplicates: duplicates }
end

# Read and parse the JSON file
file = File.read('seeds/chinese.json')
data = JSON.parse(file)

# Find duplicates
result = find_duplicates(data)

unique_items = result[:unique_items]
duplicates = result[:duplicates]

if duplicates.empty?
  puts "No duplicates found."
else
  puts "Duplicates found:"
  duplicates.each do |duplicate|
    puts "Word: #{duplicate['word']}, Meaning: #{duplicate['meaning']}, Pronunciation: #{duplicate['pronunciation']}"
  end
end

# Save the unique items and duplicates back to the JSON file in single-line format with a blank line in between
File.open('unique.json', 'w') do |f|
  f.write("[\n")
  unique_items.each_with_index do |item, index|
    f.write("  " + item.to_json)
    f.write(",\n")
  end
  f.write("\n") # Adding a blank line between unique items and duplicates
  duplicates.each_with_index do |item, index|
    f.write("  " + item.to_json)
    f.write(",\n") unless index == duplicates.length - 1
  end
  f.write("\n")
  f.write("]\n")
end

puts "\nCombined items saved to 'unique.json'."
