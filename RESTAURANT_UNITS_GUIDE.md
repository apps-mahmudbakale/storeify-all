# Restaurant Units Guide

## Overview
Storeify now includes specialized units for restaurant inventory management, making it easy to track plates, utensils, glasses, and other tableware.

## Available Units

### Restaurant Units (11 units)

| Unit Code | Unit Name | Description | Use Case |
|-----------|-----------|-------------|----------|
| `plate` | Plate | Individual plate/serving | Track plate inventory, count per service |
| `spoon` | Spoon | Individual spoon (cutlery) | Track spoon sets, replacements |
| `bottle` | Bottle | Individual bottle | Track beverages, sauces, condiments |
| `glass` | Glass | Individual glass/cup | Track glasses for drinks |
| `bowl` | Bowl | Individual bowl/dish | Track bowls, soups, sides |
| `cup` | Cup | Individual cup | Track cups for hot beverages |
| `portion` | Portion | Single serving portion | Track meal portions, pre-plated items |
| `tray` | Tray | Tray/platter | Track serving trays, platters |
| `fork` | Fork | Individual fork (cutlery) | Track fork sets, replacements |
| `knife` | Knife | Individual knife (cutlery) | Track knife sets, replacements |
| `napkin` | Napkin | Individual napkin | Track napkins, tissue, disposables |

### General Units (8 units)

| Unit Code | Unit Name | Description |
|-----------|-----------|-------------|
| `pcs` | Pieces | Individual pieces (default) |
| `box` | Box | Box unit |
| `pack` | Pack | Pack unit |
| `ml` | Milliliter | Liquid measurement |
| `l` | Liter | Liquid measurement |
| `kg` | Kilogram | Weight measurement |
| `g` | Gram | Weight measurement |
| `dozen` | Dozen | Set of 12 items |

### Medical Units (5 units)

| Unit Code | Unit Name | Description |
|-----------|-----------|-------------|
| `syringe` | Syringe | Individual syringe |
| `vial` | Vial | Individual vial |
| `tablet` | Tablet | Individual tablet |
| `capsule` | Capsule | Individual capsule |
| `ampule` | Ampule | Individual ampule |

## How to Use Restaurant Units

### Adding a Product with Restaurant Units

1. **Go to Products** → **Create New**
2. **Fill in Product Details:**
   - Name: e.g., "Dinner Plates"
   - Buying Price: Cost per item
   - Selling Price: Sale price per item (if applicable)
   - Quantity: Number of items in stock

3. **Select Unit**
   - **Unit Dropdown:** Select from restaurant units
   - Example: For plates, select "Plate"
   - Example: For bottles of water, select "Bottle"

4. **Save Product**

### Tracking Example

**Scenario:** Managing restaurant tableware

| Product | Quantity | Unit | Purpose |
|---------|----------|------|---------|
| Dinner Plates | 120 | Plate | Main course plates |
| Dessert Plates | 80 | Plate | Small dessert plates |
| Wine Glasses | 50 | Glass | Wine service |
| Coffee Cups | 100 | Cup | Hot beverage service |
| Forks (Set A) | 150 | Fork | Cutlery stock |
| Cloth Napkins | 200 | Napkin | Table napkins |
| Water Bottles | 24 | Bottle | Bottled water inventory |

### Inventory Management

**Recording Dispense/Sales:**
1. Go to **New Sale**
2. Search for product (e.g., "Dinner Plates")
3. Select quantity sold/used (e.g., 10 plates)
4. Unit automatically shows as "Plate"
5. Complete sale

**Tracking Usage:**
- Each sale automatically decrements stock
- View product history to see all transactions
- Stock closing helps identify missing items

## Stock Closing with Restaurant Units

### End-of-Service Inventory Count

1. **Go to Stock Closing** (Reports section)
2. **Select Month/Year** for the period
3. **For Each Product:**
   - View transactions (plates used, glasses broken, etc.)
   - Count actual inventory
   - Enter closing quantity
   - System calculates variance

### Example

**Dinner Plates Stock Closing:**
- Opening Qty: 120 plates
- Dispensed (sales): 45 plates
- Expected Closing: 75 plates (120 - 45)
- Actual Count: 72 plates
- Variance: -3 plates (3 broken/lost)

**Action Items:**
- Document the 3 missing plates
- Update notes: "3 plates broken during service"
- Reorder plates if below minimum

## Best Practices

### For Restaurants

1. **Daily Counts**
   - Count tableware before/after service
   - Record in stock closing system
   - Track breakage and losses

2. **Minimum Stock Levels**
   - Set min_qty for each item
   - Get alerts when below threshold
   - Plan replacement orders

3. **Lost & Damage Tracking**
   - Use notes field in stock closing
   - Document reasons for variance
   - Track breakage patterns

4. **Reordering**
   - Monitor inventory trends
   - Order replacements before stock runs out
   - Use reports to analyze usage patterns

### Unit Selection Tips

✓ Use **"Plate"** for all tableware plates (not "pcs")
✓ Use **"Glass"** specifically for drinkware
✓ Use **"Portion"** for pre-made meal items
✓ Use **"Tray"** for serving platters
✓ Use **"Bottle"** for beverages only
✓ Use **"Napkin"** for napkins and tissue products
✓ Use **"Dozen"** for bulk purchases of items

❌ Avoid mixing units for same item
❌ Don't use generic "pcs" for restaurant items
❌ Don't mix units if tracking similar items

## Reporting with Units

### View Usage by Unit

1. **General Reports** - Shows all products with their units
2. **Stock Closing Report** - View variance by unit type
3. **Product History** - Trace all transactions with unit information

### Example Queries

"How many plates were dispensed this month?"
- Filter products with "Plate" unit
- Sum qty_dispensed from transactions

"Which items have the most breakage?"
- Review stock closing variances
- Sort by variance (negative values = losses)

## API Access (Future)

If you build integrations, units are available via:

```
GET /api/products/units
GET /api/products/units?category=restaurant
GET /api/products/units?category=general
```

Response includes all 24 units with metadata.

## Troubleshooting

### Unit Not Showing in Dropdown?

1. Go to dashboard
2. Clear browser cache
3. Refresh page
4. Contact admin if still missing

### Product Shows Wrong Unit?

1. Edit product
2. Re-select correct unit
3. Save changes
4. Check history to verify change

### Need More Units?

Contact administrator to add custom units for your business needs.

---

**Restaurant Inventory Management** - Track plates, utensils, glasses, and tableware with precision.
