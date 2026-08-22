import pandas as pd
import re
from pathlib import Path

# Read the XLSX file, ensuring CODE is read as string
input_file = r'c:\Users\Ogochukwu\Downloads\1.xlsx'
df = pd.read_excel(input_file, dtype={'CODE': str})

print("Original CSV columns:", df.columns.tolist())
print("\nOriginal shape:", df.shape)
print("\nFirst row:")
print(df.iloc[0])

# Parse data
new_rows = []

for idx, row in df.iterrows():
    code = str(row['CODE']).strip()  # Convert to string to preserve formatting
    title = row['COURSE TITLE']
    dates_str = str(row.get('DATE', ''))
    venues_str = str(row.get('VENUE WITH FEES', ''))
    
    # Split by newlines
    dates = [d.strip() for d in dates_str.split('\n') if d.strip()]
    venues_fees = [v.strip() for v in venues_str.split('\n') if v.strip()]
    
    print(f"\n\nProcessing {code}:")
    print(f"Dates: {dates}")
    print(f"Venues/Fees: {venues_fees}")
    
    # Match each date with venue and fee
    for i, date in enumerate(dates):
        if i < len(venues_fees):
            venue_fee = venues_fees[i]
            
            # Parse "VENUE – $FEE" or "VENUE - $FEE"
            match = re.match(r'^(.+?)\s*[-–]\s*\$?([\d,\.]+)', venue_fee)
            if match:
                venue = match.group(1).strip()
                fee_str = match.group(2).strip()
                fee = float(fee_str.replace(',', ''))
                
                new_rows.append({
                    'CODE': code,
                    'COURSE TITLE': title,
                    'DATE': date,
                    'VENUE': venue,
                    'FEE': fee,
                    'CURRENCY': 'NGN'
                })
                
                print(f"  -> Date: {date}, Venue: {venue}, Fee: {fee}")

# Create new DataFrame
new_df = pd.DataFrame(new_rows)

# Convert FEE to integer (remove decimals)
new_df['FEE'] = new_df['FEE'].astype(int)

# Save to CSV
output_file = r'c:\Users\Ogochukwu\Downloads\1_parsed.csv'
new_df.to_csv(output_file, index=False, quoting=1)  # quoting=1 is QUOTE_ALL

print(f"\n\nParsed CSV created: {output_file}")
print(f"Total expanded rows: {len(new_rows)}")
print("\nFirst 5 rows:")
print(new_df.head())
