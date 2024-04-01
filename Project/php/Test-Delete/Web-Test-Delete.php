<!DOCTYPE html>
<html>
<head>
  <title>Generative AI with Gemini</title>
  <style>
    /* Add your styles here */
  </style>
</head>
<body>
  <p>Enter your prompt here: <input type="text" id="prompt-input"></p>
  <button id="generate-button">Generate Text</button>
  <pre id="generated-text"></pre>

  <script type="importmap">
    {
      "imports": {
        "@google/generative-ai": "https://esm.run/@google/generative-ai"
      }
    }
  </script>
  <script type="module">
    import { GoogleGenerativeAI } from "@google/generative-ai";

    // Replace "... with your actual API key from Google AI Studio
    const API_KEY = "YOUR_API_KEY_HERE";

    const genAI = new GoogleGenerativeAI(API_KEY);

    const generateButton = document.getElementById("generate-button");
    const promptInput = document.getElementById("prompt-input");
    const generatedText = document.getElementById("generated-text");

    async function generateText() {
      const prompt = promptInput.value;

      // For text-only input, use the gemini-pro model
      const model = genAI.getGenerativeModel({ model: "gemini-1.0-pro" });

    //   const generationConfig = {
    //     stopSequences: ["red"], // Stop generation when "red" is encountered
    //     maxOutputTokens: 200,  // Maximum number of tokens to generate
    //     temperature: 0.9,       // Controls randomness (higher = more creative)
    //     topP: 0.1,              // Probability distribution for next word selection
    //     topK: 16,               // Consider the top 16 most likely words
    //   };

      const generationConfig = {
      temperature: 0.9,
      topK: 1,
      topP: 1,
      maxOutputTokens: 2048,
    };

      const modelWithConfig = genAI.getGenerativeModel({ model: "gemini-1.0-pro", generationConfig });

      const result = await modelWithConfig.generateContent(prompt);
      const response = await result.response;
      const text = response.text();
      generatedText.innerText = text;
    }

    generateButton.addEventListener("click", generateText);
  </script>
</body>
</html>
