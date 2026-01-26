# Jarvis Assistant

A Python-based personal assistant inspired by Iron Man's Jarvis. It can listen to your voice and perform tasks like searching Wikipedia, opening websites, telling the time, and automating system tasks.

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

2. **Install system dependencies for Automation (Linux)**:
   - For `pyautogui` to work on Linux, you may need:
     ```bash
     sudo apt-get install python3-tk python3-dev scrot
     ```

3. **Install Python dependencies**:
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
- "Open Notepad" / "Write Notepad" (Opens a text editor)
- "Type this" (Jarvis will ask what to type and then type it into the active window)
- "Take screenshot" (Saves a screenshot to the current directory)
- "Exit" / "Quit" / "Bye"

## Note on Environments without Microphone or Display
If the script cannot access a microphone or a graphical display (e.g., in a headless server), it will gracefully fallback:
- **No Microphone**: Automatically falls back to text input mode.
- **No Display**: Automation commands (`pyautogui` based) will print an error message instead of crashing.
