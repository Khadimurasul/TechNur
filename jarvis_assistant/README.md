# Jarvis Assistant

A Python-based personal assistant inspired by Iron Man's Jarvis. It can listen to your voice and perform tasks like searching Wikipedia, opening websites, and telling the time.

## Prerequisites

- Python 3.x
- Internet connection (for Google Speech Recognition and Wikipedia)

## Installation

1. **Install PortAudio** (required for `PyAudio`):
   - **Windows**: Often included in the wheel, but you might need to install via `pip install pipwin` then `pipwin install pyaudio` if regular pip fails.
   - **Linux (Ubuntu/Debian)**:
     ```bash
     sudo apt-get install python3-pyaudio portaudio19-dev
     ```
   - **macOS**:
     ```bash
     brew install portaudio
     ```

2. **Install Python dependencies**:
   ```bash
   pip install -r requirements.txt
   ```

## Usage

Run the assistant with:
```bash
python jarvis.py
```

### Supported Commands:
- "Search Wikipedia for [topic]"
- "Open YouTube"
- "Open Google"
- "What is the time?"
- "How are you?"
- "Exit" / "Quit" / "Bye"

## Note on Environments without Microphone
If the script cannot access a microphone (e.g., in a headless server or certain sandbox environments), it will automatically fallback to text input mode where you can type your commands.
