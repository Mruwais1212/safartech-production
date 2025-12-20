# TODO List Todo

1- create service to get destination and cache theme
2- create service to get accommodation and transportation and cache theme
3- create service to images from google and sort it

1-[ ] Create Images To Destination Using Google API
2-[ ] Create Images To Destination Places Using Google API
3-[ ] Know Api Used For Booking Flights
4-[ ] know Api Used For Hotel Booking
5-[ ] Create Reservation using Two Api Or One Of Them
6-[ ] Create prebook for select booking code
7-[ ] Go to Flight Booking page and get booking code
8-[ ] Create  details
OB1[TBO]ivm9fPpU4qBJYWatkKg4oylKkKdVMwP1hGuG7nGCqOKCeHRzHjEEpdxrKP psy4qZhM0vKIoEKsacqRXNuXejEcA 0XJEAGLWP8Gku4F9ENbLuwC589LHz79/H XQuy2iwDyIu8cvSpFCnh3/r2LqBGhKmM8oaX///RlvbKzKmgoejNqV647Atrt95MfQfWWzYtWX88I0XMe9sVx2tB Q29aQHzQus2EwINhAbSggVelp5DaHaNUcA7t mFH17q2E1xRW4EVaw8zJ/nXH uO5xrxwHJ4svy74YVNerTjqsT  O96d2oDLUz8VlJMUHAxQQuGbFTDlqkTdVkVxcKlBkvBi2GrzuZTH 86F4FfNVpX0GW86bcN4oKtjaa7dKz5SE5UuyGNZOAUVSSxj vJ8X9pduj64LZJer37KJvPC/HzSaO6En6zm60kA58J4O7CuXTDfu/nVjaJmt0ydKif/Dd2VE1moXsoWLzJeXW1WBSDNgS4BRjiKXtnHPZrPPT BhCNQmmCdJ0pLvdcMyz20GyJS/8a0ueNbtD9d9U mzx/9hIVUOQkkk23pVEfvf60UfvWol/KKKV5TOhL 8urTpYTXbo/bQUMLVYYjxlTepJOsN9o8PZaVblBFbKvIep7

// Ram
// 10:45
// DEL to CCU
// BOM to DEL
// DEL to MAA
// DEL to BLR
// BLR to BOM
// Ram
// 10:47
// If the "SearchCombinationType"=1 then it is the open combination

// If the "SearchCombinationType"=2 then it is the fixed combination

workflow of reservation hotels

1. user select number of rooms and number of travelers and date
2. search for cities using (http://api.tbotechnology.in/TBOHolidays_HotelAPI/CityList)
3. search for hotels using (http://api.tbotechnology.in/TBOHolidays_HotelAPI/hotelcodelist) based on city id
4. user select hotel and booking  (using prebook) (http://api.tbotechnology.in/TBOHolidays_HotelAPI/Book)
5. user select payment method and booking  (using book) (http://api.tbotechnology.in/TBOHolidays_HotelAPI/Book)