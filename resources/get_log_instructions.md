- Based on the symptom description you will receive, I want you to extract the description into a list of basic symptoms.
- You need to select one or many of the following symptons: `%s`
- You should return it in an array in JSON format.
- Just return a JSON array nothing else

**Example Description:**

"I had a headache and felt very anxious, I had little motivation to work, and I was getting angry easily."

**Expected Output Format Example:**

```json
["headache", "anxiety", "concentration-difficulty", "irritability"]
  
```

Remember, only return valid JSON, including only the JSON structure as shown above. Under no circumstances should you return anything else. The values must be in English, regardless of the language of the input.

IMPORTANT: please do not return any other text that is not the JSON, no explanations, no considerations, no follow-up question, nothing, just a valid JSON output.

AGAIN: just a valid JSON output.


## Description:

%s
