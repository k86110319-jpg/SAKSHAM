
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAKSHAM Study Tools</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="accessibility.css">

    <style>
        .tool-container {
            max-width: 900px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px #0001;
        }

        textarea {
            width: 100%;
            min-height: 180px;
            padding: 12px;
            border: 1px solid #aaa;
            border-radius: 8px;
            font-size: 16px;
        }

        .tool-button {
            background: #174ea6;
            color: white;
            padding: 11px 18px;
            border: none;
            border-radius: 7px;
            margin: 8px 5px 8px 0;
            cursor: pointer;
        }

        .result {
            background: #eef4ff;
            padding: 18px;
            margin-top: 20px;
            border-radius: 10px;
            white-space: pre-wrap;
        }
    </style>
</head>

<body>

<div class="tool-container">

    <h1>SAKSHAM Study Assistant</h1>
    <p>Make learning easier with smart study tools.</p>

    <div class="accessibility-toolbar">
        <button onclick="changeFontSize(2)">A+</button>
        <button onclick="changeFontSize(-2)">A-</button>
        <button onclick="toggleContrast()">High Contrast</button>
        <button onclick="readPage()">🔊 Read Aloud</button>
        <button onclick="stopReading()">Stop</button>
    </div>

    <h3>Enter your study notes</h3>

    <textarea id="notes"
        placeholder="Paste your notes here..."></textarea>

    <br>

    <button class="tool-button" onclick="summarizeNotes()">
        Summarize Notes
    </button>

    <button class="tool-button" onclick="generateQuiz()">
        Generate Quiz
    </button>

    <div id="result" class="result" aria-live="polite">
        Your result will appear here.
    </div>

    <button class="tool-button" onclick="copyResult()">
        Copy Result
    </button>

    <br><br>
    <a href="dashboard.php">Back to Dashboard</a>

</div>

<script src="accessibility.js"></script>

<script>
function getSentences() {
    let text = document.getElementById("notes").value.trim();

    if (!text) {
        alert("Please enter your study notes first.");
        return [];
    }

    return text.match(/[^.!?]+[.!?]+|[^.!?]+$/g)
        ?.map(s => s.trim())
        .filter(s => s.length > 15) || [];
}

function summarizeNotes() {
    let sentences = getSentences();

    if (sentences.length === 0) return;

    let count = Math.max(1, Math.ceil(sentences.length / 3));

    let summary = sentences.slice(0, count).join(" ");

    document.getElementById("result").innerText =
        "YOUR STUDY SUMMARY\n\n" + summary;
}

function generateQuiz() {
    let sentences = getSentences();

    if (sentences.length === 0) return;

    let questions = sentences.slice(0, 5).map((sentence, index) => {
        let words = sentence.split(/\s+/);
        let answerIndex = words.findIndex(word =>
            word.replace(/[.,!?]/g, "").length > 5
        );

        if (answerIndex === -1) answerIndex = 0;

        let answer = words[answerIndex].replace(/[.,!?]/g, "");
        words[answerIndex] = "________";

        return (index + 1) + ". Complete the sentence:\n" +
            words.join(" ") + "\nAnswer: " + answer;
    });

    document.getElementById("result").innerText =
        "YOUR PRACTICE QUIZ\n\n" + questions.join("\n\n");
}

function copyResult() {
    let result = document.getElementById("result").innerText;

    navigator.clipboard.writeText(result)
        .then(() => alert("Result copied!"))
        .catch(() => alert("Could not copy. Please select and copy the text."));
}
</script>

</body>
</html>
