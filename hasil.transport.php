-- Create car table if it doesn't exist
CREATE TABLE IF NOT EXISTS pesawat (
    id_pesawat INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Insert sample data into pesawat table
INSERT INTO pesawat (name) VALUES
('Lion Air'),
('Garuda');

-- Create bookings_mobil table if it doesn't exist
CREATE TABLE IF NOT EXISTS bookings_mobil (
    id_bm INT AUTO_INCREMENT PRIMARY KEY,
    id_pesawat INT,
    id_destinations INT,
    check_in_date DATE,
    check_out_date DATE,
    rooms INT,
    detail TEXT,
    FOREIGN KEY (id_pesawat) REFERENCES pesawat(id_pesawat),
    FOREIGN KEY (id_destinations) REFERENCES destinations(id_destinations)
);

-- Insert sample data into bookings_mobil table
INSERT INTO bookings_mobil (id_pesawat, id_destinations, check_in_date, check_out_date, rooms, detail) VALUES
(1, 2, '2024-06-11', '2024-06-13', 1, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJ24BeDptA0i0RSje5zOg0c-I&from=Denpasar&date=2024-06-11&totalDays=2&hour=05&minute=00&minPickupDate=2024-06-11&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-8.670458199999999&longitude=115.2126293&carRegionalId=48830f5c-f272-4a76-998d-f288e1df790f&type=AUTOMATIC'),
(2, 2, '2024-06-12', '2024-06-14', 2, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJ24BeDptA0i0RSje5zOg0c-I&from=Denpasar&date=2024-06-12&totalDays=2&hour=05&minute=00&minPickupDate=2024-06-12&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-8.670458199999999&longitude=115.2126293&carRegionalId=48830f5c-f272-4a76-998d-f288e1df790f&type=AUTOMATIC'),
(3, 2, '2024-06-13', '2024-06-15', 1, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJ24BeDptA0i0RSje5zOg0c-I&from=Denpasar&date=2024-06-13&totalDays=2&hour=05&minute=00&minPickupDate=2024-06-13&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-8.670458199999999&longitude=115.2126293&carRegionalId=9c8bb874-5acd-4c80-97c7-709cfdb9c339&type=AUTOMATIC'),
(4, 2, '2024-06-14', '2024-06-16', 2, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJ24BeDptA0i0RSje5zOg0c-I&from=Denpasar&date=2024-06-14&totalDays=2&hour=05&minute=00&minPickupDate=2024-06-14&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-8.670458199999999&longitude=115.2126293&carRegionalId=7d8abd29-37cb-4541-af89-0bc2bafd8344&type=AUTOMATIC'),
(5, 2, '2024-06-15', '2024-06-17', 1, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJ24BeDptA0i0RSje5zOg0c-I&from=Denpasar&date=2024-06-15&totalDays=2&hour=05&minute=00&minPickupDate=2024-06-15&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-8.670458199999999&longitude=115.2126293&carRegionalId=4552b539-5ad5-44c1-a112-49ff648a43cd&type=AUTOMATIC'),
(6, 1, '2024-06-16', '2024-06-18', 2, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJf8QaOPj71y0RQL5S43Z6AgM&from=Surabaya&date=2024-06-16&totalDays=2&hour=05&minute=00&minPickupDate=2024-06-16&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-7.2574719&longitude=112.7520883&carRegionalId=fb4b26e0-b854-43f4-b0b4-e81f342470ef&type=AUTOMATIC'),
(7, 1, '2024-06-17', '2024-06-19', 1, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJf8QaOPj71y0RQL5S43Z6AgM&from=Surabaya&date=2024-06-17&totalDays=2&hour=05&minute=00&minPickupDate=2024-06-17&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-7.2574719&longitude=112.7520883&carRegionalId=f8071e02-9871-4adf-9ef7-0e6b7867960c&type=AUTOMATIC'),
(8, 1, '2024-06-18', '2024-06-20', 2, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJf8QaOPj71y0RQL5S43Z6AgM&from=Surabaya&date=2024-06-18&totalDays=2&hour=05&minute=00&minPickupDate=2024-06-18&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-7.2574719&longitude=112.7520883&carRegionalId=ae3aeb47-9293-40b1-a357-369d8ec45f78&type=AUTOMATIC'),
(9, 1, '2024-06-19', '2024-06-21', 1, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJf8QaOPj71y0RQL5S43Z6AgM&from=Surabaya&date=2024-06-19&totalDays=2&hour=05&minute=00&minPickupDate=2024-06-19&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-7.2574719&longitude=112.7520883&carRegionalId=cd3d5164-d835-4d9d-bf49-5a66181f9e3a&type=AUTOMATIC'),
(10, 1, '2024-06-20', '2024-06-22', 2, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJf8QaOPj71y0RQL5S43Z6AgM&from=Surabaya&date=2024-06-20&totalDays=2&hour=05&minute=00&minPickupDate=2024-06-20&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-7.2574719&longitude=112.7520883&carRegionalId=b56c43d1-c87b-4d0b-b2b1-f51bb0cc7052&type=AUTOMATIC');
