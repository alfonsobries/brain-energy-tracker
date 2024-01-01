Based on the food descriptions provided, organize the information in a JSON format for database storage. The details to include for each meal are:

- `calories`
- `sugar`
- `protein`
- `fat`
- `carbohydrates`
- `fiber`
- `gluten_level` (options: high, medium, low, present, none)
- `lactose_level` (options: high, medium, low, present, none)
- `common_allergens` (formatted as a JSON array)
- `description`: This should be the exact meal description you provide.

Each meal should be logged as a separate record. When specific details are not provided, use your best judgment to estimate values, doesnt need to be exact. For example, if you only mention "cake," provide typical nutritional values for a standard serving of cake. If the meal description combines two foods, combine their nutritional values accordingly. Zero (0) is a valid value for any category where applicable, such as water having zero calories. Use `null` as a value only in very rare cases when there is no information available about a food item, and you dont make an educated guess. In most situations, a value should be provided.

For `gluten_level` and `lactose_level`, use the following categories:
- `high`: For foods rich in the specific macronutrient.
- `medium`: For foods containing the macronutrient, but not in large quantities.
- `low`: For foods with a small amount of the macronutrient.
- `present`: The macronutrient is not an inherent part of the food but is typically introduced during cooking.
- `none`: The food does not contain the nutrient.

**Example Description:**

```
Breakfast: Scrambled eggs with spinach and a glass of orange juice.
Lunch: Chicken salad with mustard and honey dressing, accompanied by whole wheat bread.
Dinner: Grilled salmon fillet with quinoa and broccoli.
```

**Expected Output Format Example:**

```json
[
  {
    "name": "Breakfast",
    "main_ingredients": ["eggs", "spinach", "orange juice"],
    "calories": 250,
    "sugar": 20,
    "protein": 14,
    "fat": 15,
    "carbohydrates": 18,
    "fiber": 2,
    "common_allergens": ["eggs"],
    "gluten_level": "none",
    "lactose_level": "none",
    "description": "Scrambled eggs with spinach and a glass of orange juice."
  },
  {
    "name": "Lunch",
    "main_ingredients": ["chicken", "mustard", "honey", "whole wheat bread"],
    "calories": 350,
    "sugar": 5,
    "protein": 30,
    "fat": 8,
    "carbohydrates": 40,
    "fiber": 5,
    "common_allergens": ["wheat"],
    "gluten_level": "high",
    "lactose_level": "none",
    "description": "Chicken salad with mustard and honey dressing, accompanied by whole wheat bread."
  },
  {
    "name": "Dinner",
    "main_ingredients": ["salmon", "quinoa", "broccoli"],
    "calories": 400,
    "sugar": 3,
    "protein": 35,
    "fat": 20,
    "carbohydrates": 30,
    "fiber": 6,
    "common_allergens": ["fish"],
    "gluten_level": "none",
    "lactose_level": "none",
    "description": "Grilled salmon fillet with quinoa and broccoli."
  }
]
```

Please ensure the returned JSON is valid and structured as shown above. The output should exclusively consist of the JSON data, with no additional text or explanations. All values must be in English, regardless of the language of the input.

**IMPORTANT:** Only the JSON output should be returned. No explanations, considerations, follow-up questions, or additional text should be included.

## Food Description:

%s
