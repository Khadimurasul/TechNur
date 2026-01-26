import pyttsx3
import speech_recognition as sr
import sys
import datetime
import wikipedia
import webbrowser
import os
import subprocess
import time

try:
    import pyautogui
except Exception:
    pyautogui = None

# Initialize the TTS engine
def init_engine():
    try:
        engine = pyttsx3.init()
        voices = engine.getProperty('voices')
        if voices:
            engine.setProperty('voice', voices[0].id)
        engine.setProperty('rate', 175)
        return engine
    except Exception as e:
        print(f"TTS Engine could not be initialized: {e}")
        return None

engine = init_engine()

def speak(text):
    """Converts text to speech and prints it."""
    print(f"Jarvis: {text}")
    if engine:
        try:
            engine.say(text)
            engine.runAndWait()
        except Exception:
            pass

def listen(manual_input=False):
    """Listens for audio input and returns recognized text."""
    if manual_input:
        return input("User: ").lower()

    r = sr.Recognizer()
    try:
        with sr.Microphone() as source:
            print("Listening...")
            r.pause_threshold = 0.8
            audio = r.listen(source, timeout=5, phrase_time_limit=5)

        print("Recognizing...")
        query = r.recognize_google(audio, language='en-US')
        print(f"User said: {query}")
        return query.lower()
    except Exception:
        return input("User (text input): ").lower()

def wish_me():
    """Greets the user based on the time of day."""
    hour = int(datetime.datetime.now().hour)
    if 0 <= hour < 12:
        speak("Good Morning!")
    elif 12 <= hour < 18:
        speak("Good Afternoon!")
    else:
        speak("Good Evening!")
    speak("I am Jarvis. How can I assist you today?")

def handle_command(query):
    """Handles the recognized command."""
    if not query or query == "none":
        return

    if 'wikipedia' in query:
        speak('Searching Wikipedia...')
        query = query.replace("wikipedia", "")
        try:
            results = wikipedia.summary(query, sentences=2)
            speak("According to Wikipedia")
            speak(results)
        except Exception:
            speak("I couldn't find anything on Wikipedia for that.")

    elif 'open youtube' in query:
        speak("Opening YouTube")
        webbrowser.open("https://www.youtube.com")

    elif 'open google' in query:
        speak("Opening Google")
        webbrowser.open("https://www.google.com")

    elif 'the time' in query:
        strTime = datetime.datetime.now().strftime("%H:%M:%S")
        speak(f"Sir, the time is {strTime}")

    elif 'how are you' in query:
        speak("I am doing well, thank you for asking. I am ready to help.")

    elif 'open notepad' in query or 'write notepad' in query:
        speak("Opening Notepad, Sir.")
        if sys.platform == "win32":
            subprocess.Popen(['notepad.exe'])
        elif sys.platform == "darwin":
            subprocess.Popen(['open', '-a', 'TextEdit'])
        else:
            # Linux fallback
            try:
                subprocess.Popen(['gedit'])
            except FileNotFoundError:
                speak("I couldn't find a graphical text editor, but I can try opening nano in a new terminal if you'd like.")

    elif 'type this' in query:
        speak("What should I type, Sir?")
        content = listen()
        if content:
            speak("Typing now...")
            if pyautogui:
                try:
                    pyautogui.write(content, interval=0.1)
                except Exception as e:
                    speak(f"I encountered an error while typing: {e}")
            else:
                speak("PyAutoGUI is not available, so I cannot type for you.")

    elif 'take screenshot' in query:
        speak("Taking a screenshot, Sir.")
        if pyautogui:
            try:
                timestamp = datetime.datetime.now().strftime("%Y-%m-%d_%H-%M-%S")
                filename = f"screenshot_{timestamp}.png"
                pyautogui.screenshot(filename)
                speak(f"Screenshot saved as {filename}")
            except Exception as e:
                speak(f"I couldn't take a screenshot: {e}")
        else:
            speak("PyAutoGUI is not available.")

    elif 'exit' in query or 'quit' in query or 'bye' in query:
        speak("Goodbye Sir! Have a productive day.")
        sys.exit()

    else:
        speak("I'm not sure I understand that command yet. I'm still learning.")

def main():
    wish_me()
    while True:
        query = listen()
        handle_command(query)

if __name__ == "__main__":
    main()
