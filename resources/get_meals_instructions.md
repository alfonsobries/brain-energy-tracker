Based on the food descriptions you will receive, organize the relevant information in a JSON format for database storage. The required details are:

- calories
- sugar
- protein
- fat
- carbohydrates
- fiber
- common_allergens (in a JSON array format)

Each meal should be a separate record. In the absence of specific details, provide estimates based on your knowledge, for example, typical nutritional values of a cake or an average amount of meat consumed in a meal. Exceptions for returning a null value are minimal, only when there is no information available about a food.

**Example Description:**

"For breakfast, I had scrambled eggs with spinach and a glass of orange juice. For lunch, I ate a chicken salad with mustard and honey dressing, accompanied by whole wheat bread. For dinner, I opted for a grilled salmon fillet with quinoa and broccoli."

**Expected Output Format Example:**

```json
[
  {
    "meal": "Breakfast",
    "main_ingredients": ["eggs", "spinach", "orange juice"],
    "calories": 250,
    "sugar": 20,
    "protein": 14,
    "fat": 15,
    "carbohydrates": 18,
    "fiber": 2,
    "common_allergens": ["eggs"],
  },
  {
    "meal": "Lunch",
    "main_ingredients": ["chicken", "mustard", "honey", "whole wheat bread"],
    "calories": 350,
    "sugar": 5,
    "protein": 30,
    "fat": 8,
    "carbohydrates": 40,
    "fiber": 5,
    "common_allergens": ["chicken", "mustard", "wheat"],
  },
  {
    "meal": "Dinner",
    "main_ingredients": ["salmon", "quinoa", "broccoli"],
    "calories": 400,
    "sugar": 3,
    "protein": 35,
    "fat": 20,
    "carbohydrates": 30,
    "fiber": 6,
    "common_allergens": ["fish"],
  }
]
```

Remember, only return valid JSON, including only the JSON structure as shown above, under any circustance you should return anything else. The values must be in English, regardless of the language of the input.

IMPORTANT: please dont return any other text that is not the JSON, no explanations, no considerations, nothing.


## Food description:

%s