import pandas as pd
import random
from datetime import datetime, timedelta

def generate_purchase_data(filename="large_purchase_order.xlsx", num_rows=1000):
    categories = ['Antibiotics', 'Painkillers', 'Vitamins', 'First Aid', 'Supplements', 'Cardiovascular', 'Dermatology']
    products = [
        'Amoxicillin', 'Paracetamol', 'Ibuprofen', 'Vitamin C', 'Bandages', 
        'Omeprazole', 'Metformin', 'Amlodipine', 'Cetirizine', 'Loratadine',
        'Ciprofloxacin', 'Azithromycin', 'Doxycycline', 'Aspirin', 'Diclofenac'
    ]
    
    data = []
    
    for i in range(num_rows):
        product_base = random.choice(products)
        category = random.choice(categories)
        
        # Generate variations to simulate many products
        product_name = f"{product_base} {random.choice(['250mg', '500mg', '1000mg'])} - {random.randint(1, 100)}"
        
        quantity = random.randint(10, 500)
        buying_price = random.randint(100, 5000)
        selling_price = int(buying_price * random.uniform(1.2, 1.5))
        
        batch_num = f"BATCH-{datetime.now().strftime('%Y%m')}-{random.randint(1000, 9999)}"
        
        # Random expiry date between 1 and 3 years from now
        days_future = random.randint(365, 1095)
        expiry_date = (datetime.now() + timedelta(days=days_future)).strftime('%Y-%m-%d')
        
        data.append({
            'Product Name': product_name,
            'Category': category,
            'Quantity': quantity,
            'Buying Price': buying_price,
            'Selling Price': selling_price,
            'Batch Number': batch_num,
            'Expiry Date (YYYY-MM-DD)': expiry_date
        })
            
    df = pd.DataFrame(data)
    df.to_excel(filename, index=False)
    print(f"Successfully generated {filename} with {num_rows} rows.")

if __name__ == "__main__":
    try:
        generate_purchase_data()
    except ImportError:
        print("Error: pandas or openpyxl not found. Please install them using: pip install pandas openpyxl")

