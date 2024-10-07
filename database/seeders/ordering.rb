# Load the file contents as plain text
file_path = 'seeds/chinese.json'
file_content = File.read(file_path)

# Add a comma after every closing brace '}'
modified_content = file_content.gsub(/}/, '},')

# Remove the last comma to avoid a trailing one at the end of the file

# Write the changes back to the same file
File.open(file_path, 'w') do |f|
  f.write(modified_content)
end

puts "Commas added after every } in #{file_path}!"
