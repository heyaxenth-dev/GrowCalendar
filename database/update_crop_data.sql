-- Update crop data to match the comprehensive dataset from Barbaza, Antique
-- This replaces the existing crop data with the detailed 50 crops

-- Clear existing crop data
DELETE FROM crop_soil_compatibility;
DELETE FROM crops;

-- Update soil types to match the dataset
DELETE FROM soil_types;
INSERT INTO soil_types (name, description, ph_min, ph_max, drainage, fertility_level) VALUES
('Alluvial clay loam', 'Rich alluvial soil with good water retention', 6.0, 7.5, 'Moderate', 'High'),
('Loam to sandy loam', 'Balanced soil with good drainage', 6.0, 7.0, 'Good', 'Medium'),
('Sandy loam, well-drained coastals', 'Light soil with excellent drainage', 5.5, 7.0, 'Excellent', 'Low'),
('Loam, clay loam, alluvial soils', 'Heavy soil with good fertility', 6.0, 7.5, 'Moderate', 'High'),
('Well-drained loam', 'Ideal soil with balanced properties', 6.0, 7.0, 'Good', 'High'),
('Sandy loam to loam', 'Light to medium soil', 5.5, 7.0, 'Good', 'Medium'),
('Clay to silty clay (moist soils)', 'Heavy soil with high water retention', 6.0, 7.5, 'Poor', 'High'),
('Sandy loam, well-drained', 'Light soil with good drainage', 5.5, 7.0, 'Excellent', 'Low'),
('Loam, fertile garden soil', 'Rich soil ideal for vegetables', 6.0, 7.0, 'Good', 'High'),
('Loam, well-drained', 'Balanced soil with good drainage', 6.0, 7.0, 'Good', 'Medium'),
('Deep loam, well-drained', 'Deep soil with excellent properties', 6.0, 7.0, 'Good', 'High'),
('Sandy loam, well-drained acidic soil', 'Acidic soil suitable for specific crops', 4.5, 6.5, 'Excellent', 'Low'),
('Loam with good organic matter', 'Rich organic soil', 6.0, 7.0, 'Good', 'Very High'),
('Deep loam to clay loam', 'Deep heavy soil', 6.0, 7.5, 'Moderate', 'High'),
('Wide range; loam preferred', 'Versatile soil type', 5.5, 7.0, 'Good', 'Medium');

-- Insert all 50 crops with detailed factors
INSERT INTO crops (name, scientific_name, description, planting_season, harvest_days, water_requirements, temperature_min, temperature_max, humidity_min, humidity_max, rainfall_min, rainfall_max, ph_min, ph_max, marketability, soil_type_preference, weather_conditions) VALUES
-- Staple Crops
('Rice (Palay)', 'Oryza sativa', 'Staple grain crop requiring flooded conditions', 'Wet Season', 120, 'High', 20.0, 35.0, 70.0, 90.0, 1000.0, 2000.0, 6.0, 7.5, 'Local & Provincial (staple); high demand', 'Alluvial clay loam, Sta. Rita clay', 'Tropical, wet season May-Nov, dry Feb-Apr'),
('Corn (Maize)', 'Zea mays', 'Versatile grain crop for food and feed', 'Dry Season', 90, 'Medium', 15.0, 30.0, 50.0, 80.0, 500.0, 1000.0, 6.0, 7.5, 'Local & Provincial', 'Loam to sandy loam', 'Tropical warm; tolerant of variable rainfall'),
('Coconut', 'Cocos nucifera', 'Tropical palm tree for oil and copra', 'Year-round', 365, 'Low to Medium', 20.0, 35.0, 60.0, 90.0, 800.0, 1500.0, 5.5, 7.0, 'National & Export (copra, oil)', 'Sandy loam, well-drained coastals', 'Tropical humid; prefers coastal areas'),
('Sugarcane', 'Saccharum officinarum', 'Industrial crop for sugar production', 'Wet Season', 365, 'High', 20.0, 35.0, 60.0, 85.0, 1200.0, 2000.0, 6.0, 7.5, 'Provincial & National (sugar industry)', 'Loam, clay loam, alluvial soils', 'Warm, high rainfall during growing season'),

-- Banana Varieties
('Banana - Saba', 'Musa acuminata', 'Cooking banana variety', 'Year-round', 300, 'High', 20.0, 35.0, 70.0, 90.0, 1000.0, 1500.0, 6.0, 7.0, 'Local & Provincial (cooking banana)', 'Loam to sandy loam', 'Tropical humid'),
('Banana - Cavendish', 'Musa acuminata', 'Export banana variety', 'Year-round', 300, 'High', 20.0, 35.0, 70.0, 90.0, 1000.0, 1500.0, 6.0, 7.0, 'National & Export potential', 'Well-drained loam', 'Tropical humid; needs regular rainfall'),

-- Root Crops
('Sweet potato (Kamote)', 'Ipomoea batatas', 'Nutritious root crop', 'Wet Season', 120, 'Low to Medium', 20.0, 30.0, 60.0, 80.0, 600.0, 1200.0, 5.5, 6.5, 'Local markets, processed products', 'Sandy loam to loam', 'Tropical; tolerates drought'),
('Cassava (Kamoteng kahoy)', 'Manihot esculenta', 'Drought-tolerant root crop', 'Dry Season', 300, 'Low to Medium', 20.0, 35.0, 50.0, 80.0, 300.0, 800.0, 5.0, 7.0, 'Local & Industrial (starch)', 'Sandy loam to clay loam', 'Tropical; tolerant of poor soils'),
('Taro (Gabi)', 'Colocasia esculenta', 'Wetland root crop', 'Wet Season', 180, 'High', 20.0, 30.0, 70.0, 90.0, 1000.0, 1500.0, 5.5, 7.0, 'Local markets, specialty', 'Clay to silty clay (moist soils)', 'Warm, humid; suits flooded/irrigated fields'),
('Purple yam (Ube)', 'Dioscorea alata', 'High-value root crop', 'Wet Season', 240, 'Medium', 20.0, 30.0, 60.0, 80.0, 800.0, 1200.0, 6.0, 7.0, 'High local demand (value-added product)', 'Loam to sandy loam', 'Tropical; warm and humid'),

-- Legumes
('Peanut (Groundnut)', 'Arachis hypogaea', 'Oilseed legume', 'Dry Season', 120, 'Low to Medium', 20.0, 30.0, 50.0, 70.0, 400.0, 800.0, 6.0, 7.0, 'Local & Regional', 'Sandy loam, well-drained', 'Warm; prefers dryer spells for harvesting'),
('Mungbean (Munggo)', 'Vigna radiata', 'Protein-rich legume', 'Dry Season', 60, 'Low to Medium', 20.0, 30.0, 50.0, 70.0, 300.0, 600.0, 6.0, 7.0, 'Local staple; good demand', 'Loam to sandy loam', 'Warmer, drier windows best for harvest'),
('Soybean', 'Glycine max', 'Industrial legume crop', 'Dry Season', 90, 'Medium', 18.0, 30.0, 50.0, 80.0, 500.0, 1000.0, 6.0, 7.0, 'Local & industrial uses', 'Loam; well-drained', 'Warm season crop'),

-- Vegetables
('Eggplant (Talong)', 'Solanum melongena', 'Tropical vegetable crop', 'Dry Season', 100, 'Medium', 20.0, 30.0, 65.0, 85.0, 500.0, 900.0, 5.5, 7.0, 'Local markets', 'Loam, fertile garden soil', 'Warm tropical; needs moisture'),
('Tomato', 'Solanum lycopersicum', 'Popular vegetable crop', 'Dry Season', 75, 'Medium', 18.0, 28.0, 60.0, 80.0, 400.0, 800.0, 6.0, 7.0, 'Local & Regional markets', 'Loam, well-drained', 'Warm; prefers dryer spells for harvesting'),
('Chili pepper (Siling labuyo)', 'Capsicum frutescens', 'Spicy condiment crop', 'Dry Season', 90, 'Low to Medium', 20.0, 35.0, 50.0, 70.0, 300.0, 600.0, 6.0, 7.5, 'High local demand; value product', 'Loam to sandy loam', 'Warm; tolerates high temperatures'),
('Okra', 'Abelmoschus esculentus', 'Heat-tolerant vegetable', 'Dry Season', 60, 'Low to Medium', 25.0, 35.0, 50.0, 70.0, 300.0, 600.0, 6.0, 7.5, 'Local markets', 'Loam, sandy loam', 'Tropical warm; drought-tolerant variety available'),
('Squash (Kalabasa)', 'Cucurbita maxima', 'Versatile gourd family crop', 'Dry Season', 80, 'Medium', 18.0, 28.0, 60.0, 80.0, 400.0, 800.0, 6.0, 7.0, 'Local & Provincial markets', 'Loam, fertile garden soil', 'Warm; needs regular moisture'),
('Watermelon', 'Citrullus lanatus', 'Summer fruit crop', 'Dry Season', 90, 'Medium to High', 20.0, 30.0, 50.0, 70.0, 400.0, 800.0, 6.0, 7.0, 'Local seasonal demand', 'Sandy loam, well-drained', 'Warm; with dry spells preferred at harvest'),
('Cucumber', 'Cucumis sativus', 'Cool-season vegetable', 'Dry Season', 60, 'Medium', 18.0, 28.0, 60.0, 80.0, 400.0, 800.0, 6.0, 7.0, 'Local markets', 'Loam, well-drained', 'Warm; needs regular water'),
('Cabbage', 'Brassica oleracea', 'Cool-season leafy vegetable', 'Dry Season', 90, 'Medium', 15.0, 25.0, 60.0, 80.0, 500.0, 1000.0, 6.0, 7.0, 'Local & Regional', 'Loam, fertile soil (often higher elevation)', 'Cooler sites preferred (upland)'),
('Onion (bulb/green)', 'Allium cepa', 'Bulb vegetable crop', 'Dry Season', 120, 'Low to Medium', 15.0, 25.0, 50.0, 70.0, 300.0, 600.0, 6.0, 7.0, 'Regional demand', 'Loam, sandy loam', 'Upland/cooler areas better for bulb onions'),
('Garlic', 'Allium sativum', 'Bulb spice crop', 'Dry Season', 150, 'Low to Medium', 15.0, 25.0, 50.0, 70.0, 300.0, 600.0, 6.0, 7.0, 'Local & regional', 'Loam, well-drained', 'Coolish period for proper bulb formation'),
('Bell pepper (Capsicum)', 'Capsicum annuum', 'Sweet pepper variety', 'Dry Season', 90, 'Medium to High', 18.0, 28.0, 60.0, 80.0, 400.0, 800.0, 6.0, 7.0, 'High local/retail demand', 'Loam, fertile, well-drained', 'Warm; controlled moisture important'),
('Bitter gourd (Ampalaya)', 'Momordica charantia', 'Medicinal vegetable', 'Dry Season', 90, 'Medium', 20.0, 30.0, 60.0, 80.0, 400.0, 800.0, 6.0, 7.0, 'Local markets; medicinal demand', 'Loam to sandy loam', 'Tropical; warm'),
('Yardlong bean (Sitaw)', 'Vigna unguiculata', 'Climbing legume vegetable', 'Dry Season', 60, 'Medium', 20.0, 30.0, 60.0, 80.0, 400.0, 800.0, 6.0, 7.0, 'Local & market vegetable demand', 'Loam, fertile garden soil', 'Tropical; warm'),
('Winged bean (Sigarilyas)', 'Psophocarpus tetragonolobus', 'Protein-rich climbing bean', 'Dry Season', 90, 'Medium', 20.0, 30.0, 60.0, 80.0, 400.0, 800.0, 6.0, 7.0, 'Local markets; protein-rich legume', 'Loam, well-drained', 'Tropical warm; climbs supports'),

-- Spices and Herbs
('Ginger', 'Zingiber officinale', 'Rhizome spice crop', 'Year-round', 240, 'Medium to High', 20.0, 30.0, 70.0, 90.0, 800.0, 1200.0, 6.0, 7.0, 'High value, local & export processing', 'Loam, well-drained, rich organic matter', 'Warm, humid; shade tolerant'),
('Turmeric', 'Curcuma longa', 'Rhizome spice crop', 'Year-round', 240, 'Medium', 20.0, 30.0, 70.0, 90.0, 800.0, 1200.0, 6.0, 7.0, 'Growing demand for spice and health products', 'Loam, rich organic soil', 'Warm, humid; similar to ginger'),
('Lemongrass (Tanglad)', 'Cymbopogon citratus', 'Aromatic grass', 'Year-round', 90, 'Low to Medium', 20.0, 35.0, 50.0, 80.0, 400.0, 800.0, 6.0, 7.0, 'Local, essential oil potential', 'Sandy loam to loam', 'Tropical; hardy and drought-tolerant'),

-- Fruits
('Calamansi', 'Citrus microcarpa', 'Small citrus fruit', 'Year-round', 365, 'Medium', 20.0, 30.0, 60.0, 80.0, 600.0, 1200.0, 6.0, 7.0, 'High local demand; juice & condiments', 'Loam, sandy loam, well-drained', 'Tropical humid; fruiting year-round'),
('Mango', 'Mangifera indica', 'Tropical fruit tree', 'Year-round', 365, 'Low to Medium', 20.0, 35.0, 50.0, 80.0, 600.0, 1200.0, 6.0, 7.0, 'Local, provincial, export potential for certain varieties', 'Well-drained loam; sandy loam', 'Tropical; distinct dry season aids flowering'),
('Pineapple', 'Ananas comosus', 'Tropical fruit crop', 'Year-round', 365, 'Low to Medium', 20.0, 35.0, 50.0, 80.0, 600.0, 1200.0, 4.5, 6.5, 'High as fruit & processing', 'Sandy loam, well-drained acidic soil', 'Tropical; drought-tolerant once established'),
('Papaya', 'Carica papaya', 'Fast-growing fruit tree', 'Year-round', 365, 'Medium to High', 20.0, 35.0, 60.0, 80.0, 800.0, 1200.0, 6.0, 7.0, 'Local & regional', 'Loam, well-drained', 'Warm, humid; continuous fruiting possible'),
('Jackfruit', 'Artocarpus heterophyllus', 'Large tropical fruit', 'Year-round', 365, 'Medium', 20.0, 35.0, 60.0, 80.0, 800.0, 1200.0, 6.0, 7.0, 'Local markets; value-added products', 'Loam to sandy loam, deep soils', 'Tropical humid'),
('Rambutan', 'Nephelium lappaceum', 'Tropical fruit tree', 'Year-round', 365, 'High', 20.0, 35.0, 70.0, 90.0, 1000.0, 1500.0, 6.0, 7.0, 'Local & provincial seasonal fruit', 'Deep loam, well-drained', 'Humid tropical; wet season growth'),
('Lanzones', 'Lansium parasiticum', 'Tropical fruit tree', 'Year-round', 365, 'Medium to High', 20.0, 30.0, 70.0, 90.0, 800.0, 1200.0, 6.0, 7.0, 'Local specialty fruit', 'Loam, well-drained', 'Tropical; humid; specific microclimates favorable'),
('Sugar apple (Atis)', 'Annona squamosa', 'Tropical fruit tree', 'Year-round', 365, 'Medium', 20.0, 35.0, 60.0, 80.0, 600.0, 1000.0, 6.0, 7.0, 'Local fruit markets', 'Loam, well-drained', 'Tropical; warm humid'),
('Citrus (Orange/Mandarin)', 'Citrus spp.', 'Citrus fruit trees', 'Year-round', 365, 'Medium', 15.0, 30.0, 50.0, 80.0, 600.0, 1200.0, 6.0, 7.0, 'Local & regional', 'Loam with good drainage', 'Tropical to subtropical microclimates (upland)'),
('Avocado', 'Persea americana', 'Tropical fruit tree', 'Year-round', 365, 'Medium', 15.0, 30.0, 50.0, 80.0, 600.0, 1200.0, 6.0, 7.0, 'Growing market; higher value', 'Deep loam, well-drained', 'Upland cooler pockets preferred for some varieties'),

-- Industrial Crops
('Coffee (Robusta)', 'Coffea canephora', 'Coffee plantation crop', 'Year-round', 365, 'Medium', 15.0, 30.0, 60.0, 80.0, 800.0, 1500.0, 6.0, 7.0, 'Regional & specialty markets', 'Loam, well-drained upland soils', 'Upland cooler sites preferred for quality; robust'),
('Cacao (Cocoa)', 'Theobroma cacao', 'Chocolate tree crop', 'Year-round', 365, 'High', 20.0, 30.0, 70.0, 90.0, 1000.0, 1500.0, 6.0, 7.0, 'High-value (chocolate supply chain)', 'Loam with good organic matter, shade', 'Humid tropical; shade-grown systems work best'),
('Abaca', 'Musa textilis', 'Fiber crop', 'Year-round', 365, 'Medium', 20.0, 35.0, 60.0, 80.0, 800.0, 1200.0, 6.0, 7.0, 'Fiber industry (national & export)', 'Loam, well-drained slopes', 'Tropical humid; thrives in upland/gullied areas'),
('Rubber', 'Hevea brasiliensis', 'Latex-producing tree', 'Year-round', 365, 'Medium', 20.0, 35.0, 60.0, 80.0, 800.0, 1200.0, 6.0, 7.0, 'Industrial, regional demand', 'Deep loam to clay loam with good drainage', 'Tropical humid; long-term plantation crop'),
('Bamboo (shoots)', 'Bambusa spp.', 'Fast-growing grass', 'Year-round', 90, 'Medium', 15.0, 35.0, 50.0, 80.0, 600.0, 1200.0, 6.0, 7.0, 'Local construction, crafts, shoots for food', 'Wide range; loam preferred', 'Tropical; tolerant and fast-growing'),

-- Specialty Crops
('Strawberry (small-scale)', 'Fragaria × ananassa', 'Cool-season fruit', 'Dry Season', 90, 'High', 10.0, 25.0, 60.0, 80.0, 600.0, 1000.0, 6.0, 7.0, 'Niche/local high-value market', 'Sandy loam, well-drained (usually high organic matter)', 'Cooler highland microclimates required'),
('Mushroom (cultivated)', 'Pleurotus spp.', 'Indoor cultivated crop', 'Year-round', 30, 'High', 15.0, 25.0, 80.0, 95.0, 0.0, 0.0, 6.0, 7.0, 'High local demand; value-added', 'Not soil-grown (substrate)', 'Indoor-controlled; any climate if facility provides'),
('Areca nut (Betel nut)', 'Areca catechu', 'Cultural palm crop', 'Year-round', 365, 'Medium', 20.0, 35.0, 60.0, 80.0, 600.0, 1200.0, 6.0, 7.0, 'Local cultural demand', 'Loam, well-drained; coastal to inland', 'Tropical humid'),
('Sesame (Til)', 'Sesamum indicum', 'Oilseed crop', 'Dry Season', 90, 'Low', 20.0, 35.0, 40.0, 70.0, 200.0, 600.0, 6.0, 7.0, 'Niche/local markets', 'Sandy loam, well-drained', 'Hot, dry spells favorable at harvest'),
('Melon (Cantaloupe)', 'Cucumis melo', 'Summer fruit crop', 'Dry Season', 90, 'Medium', 20.0, 30.0, 50.0, 70.0, 400.0, 800.0, 6.0, 7.0, 'Local seasonal demand', 'Sandy loam, well-drained', 'Warm; dry period near harvest preferred');

-- Insert crop-soil compatibility data based on the dataset
INSERT INTO crop_soil_compatibility (crop_id, soil_type_id, compatibility_score, notes) VALUES
-- Rice compatibility
(1, 1, 0.95, 'Rice grows excellently in alluvial clay loam'),
(1, 4, 0.90, 'Rice also suitable in clay loam, alluvial soils'),

-- Corn compatibility  
(2, 2, 0.90, 'Corn ideal in loam to sandy loam'),
(2, 6, 0.85, 'Corn good in sandy loam to loam'),

-- Coconut compatibility
(3, 3, 0.95, 'Coconut perfect for sandy loam, well-drained coastals'),
(3, 2, 0.80, 'Coconut also suitable in loam to sandy loam'),

-- Sugarcane compatibility
(4, 4, 0.95, 'Sugarcane ideal in loam, clay loam, alluvial soils'),
(4, 1, 0.90, 'Sugarcane also good in alluvial clay loam'),

-- Banana compatibility
(5, 2, 0.90, 'Saba banana ideal in loam to sandy loam'),
(6, 5, 0.95, 'Cavendish banana perfect in well-drained loam'),

-- Root crops compatibility
(7, 6, 0.90, 'Sweet potato ideal in sandy loam to loam'),
(8, 6, 0.85, 'Cassava good in sandy loam to clay loam'),
(9, 7, 0.95, 'Taro perfect in clay to silty clay moist soils'),
(10, 6, 0.90, 'Purple yam ideal in loam to sandy loam'),

-- Legumes compatibility
(11, 8, 0.90, 'Peanut ideal in sandy loam, well-drained'),
(12, 2, 0.85, 'Mungbean good in loam to sandy loam'),
(13, 5, 0.90, 'Soybean ideal in loam, well-drained'),

-- Vegetables compatibility
(14, 9, 0.95, 'Eggplant perfect in loam, fertile garden soil'),
(15, 5, 0.90, 'Tomato ideal in loam, well-drained'),
(16, 2, 0.85, 'Chili pepper good in loam to sandy loam'),
(17, 2, 0.90, 'Okra ideal in loam, sandy loam'),
(18, 9, 0.95, 'Squash perfect in loam, fertile garden soil'),
(19, 8, 0.90, 'Watermelon ideal in sandy loam, well-drained'),
(20, 5, 0.90, 'Cucumber ideal in loam, well-drained'),
(21, 9, 0.85, 'Cabbage good in loam, fertile soil'),
(22, 2, 0.85, 'Onion good in loam, sandy loam'),
(23, 5, 0.90, 'Garlic ideal in loam, well-drained'),
(24, 9, 0.95, 'Bell pepper perfect in loam, fertile, well-drained'),
(25, 2, 0.85, 'Bitter gourd good in loam to sandy loam'),
(26, 9, 0.90, 'Yardlong bean ideal in loam, fertile garden soil'),
(27, 5, 0.90, 'Winged bean ideal in loam, well-drained'),

-- Spices compatibility
(28, 13, 0.95, 'Ginger perfect in loam with good organic matter'),
(29, 13, 0.95, 'Turmeric perfect in loam, rich organic soil'),
(30, 2, 0.85, 'Lemongrass good in sandy loam to loam'),

-- Fruits compatibility
(31, 2, 0.90, 'Calamansi ideal in loam, sandy loam, well-drained'),
(32, 5, 0.90, 'Mango ideal in well-drained loam; sandy loam'),
(33, 12, 0.95, 'Pineapple perfect in sandy loam, well-drained acidic soil'),
(34, 5, 0.90, 'Papaya ideal in loam, well-drained'),
(35, 6, 0.90, 'Jackfruit ideal in loam to sandy loam, deep soils'),
(36, 11, 0.95, 'Rambutan perfect in deep loam, well-drained'),
(37, 5, 0.90, 'Lanzones ideal in loam, well-drained'),
(38, 5, 0.90, 'Sugar apple ideal in loam, well-drained'),
(39, 5, 0.90, 'Citrus ideal in loam with good drainage'),
(40, 11, 0.95, 'Avocado perfect in deep loam, well-drained'),

-- Industrial crops compatibility
(41, 14, 0.90, 'Coffee ideal in loam, well-drained upland soils'),
(42, 13, 0.95, 'Cacao perfect in loam with good organic matter, shade'),
(43, 5, 0.90, 'Abaca ideal in loam, well-drained slopes'),
(44, 14, 0.90, 'Rubber ideal in deep loam to clay loam with good drainage'),
(45, 15, 0.85, 'Bamboo good in wide range; loam preferred'),

-- Specialty crops compatibility
(46, 8, 0.90, 'Strawberry ideal in sandy loam, well-drained'),
(47, 5, 0.90, 'Areca nut ideal in loam, well-drained'),
(48, 8, 0.90, 'Sesame ideal in sandy loam, well-drained'),
(49, 8, 0.90, 'Melon ideal in sandy loam, well-drained');
