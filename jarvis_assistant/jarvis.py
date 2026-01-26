import pyttsx3
import speech_recognition as sr
import sys
import datetime
import wikipedia
import webbrowser
import os

# Initialize the TTS engine
def init_engine():
    try:
        engine = pyttsx3.init()
        voices = engine.getProperty('voices')
        if voices:
            # Try to find a male voice or just use the first one
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
        except Exception as e:
            # Fallback for headless environments or errors
            pass

def listen(manual_input=False):
    """Listens for audio input and returns recognized text.
    If manual_input is True or microphone is unavailable, it uses text input.
    """
    if manual_input:
        user_input = input("User: ")
        return user_input.lower()

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
        # If microphone fails or recognition fails, fallback to text input
        # In a real GUI/CLI app, we might want to prompt the user
        user_input = input("User (text input): ")
        return user_input.lower()

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
        except Exception as e:
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

    elif 'exit' in query or 'quit' in query or 'bye' in query:
        speak("Goodbye Sir! Have a productive day.")
        sys.exit()

    else:
        speak("I'm not sure I understand that command yet. I'm still learning.")

def main():
    wish_me()
    while True:
        # Use manual_input=True if running in an environment without audio
        # For this demo, we'll try listen() which has a fallback
        query = listen()
        handle_command(query)

if __name__ == "__main__":
    main()
