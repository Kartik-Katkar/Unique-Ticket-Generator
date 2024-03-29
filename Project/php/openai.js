// Import the Axios library
const axios = require('axios');

// Replace 'YOUR_API_KEY' with your actual OpenAI API key
const apiKey = 'YOUR_API_KEY (replace with your actual API key)';
const apiUrl = 'https://api.openai.com/v1/engines/davinci-codex/completions';

// Function to generate a customized greeting message
async function generateGreetingMessage(userName) {
  // Customize the prompt based on the user's name
  const prompt = `Hey ${userName}! 🌟 Best of luck for the Codeflix Hackathon! 🚀 (give similar customised message)`;

  // Call the OpenAI API
  try {
    const response = await axios.post(apiUrl, {
      prompt: prompt,
      max_tokens: 100,
      n: 1,
    }, {
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${apiKey}`,
      },
    });

    // Extract and return the generated message
    const generatedMessage = response.data.choices[0].text.trim();
    return generatedMessage;
  } catch (error) {
    console.error('Error calling the ChatGPT API:', error.message);
    return null;
  }
}

// Example usage
const userName = 'John';
generateGreetingMessage(userName)
  .then(greetingMessage => {
    // Print the generated greeting message
    console.log(greetingMessage);
  })
  .catch(err => {
    console.error('Error:', err.message);
  });
