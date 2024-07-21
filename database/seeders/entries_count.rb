require 'json'

# Read the JSON file
file_path = 'flashcards.json'
json_data = File.read(file_path)

# Remove empty lines and parse JSON
json_data = json_data.split("\n").reject(&:empty?).join
parsed_data = JSON.parse(json_data)

# Count the number of entries
entry_count = parsed_data.length

puts "The number of entries in the JSON file is: #{entry_count}"
