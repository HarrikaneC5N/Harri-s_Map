from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service as ChromeService
from selenium.webdriver.chrome.options import Options
from webdriver_manager.chrome import ChromeDriverManager
import time

# Configurer les options du navigateur
chrome_options = Options()
chrome_options.add_argument("--headless")  # Exécuter en mode headless (sans interface graphique)

# Installer et lancer le WebDriver
service = ChromeService(executable_path=ChromeDriverManager().install())
driver = webdriver.Chrome(service=service, options=chrome_options)

# Ouvrir la page web
driver.get("http://127.0.0.1:8000/login")

# Attendre que la page se charge complètement
time.sleep(3)

# Vérifier la présence d'un élément spécifique (par exemple, le titre de la page)
assert "Log in!" in driver.title

# Prendre une capture d'écran
driver.save_screenshot("capture_homepage.png")

# Fermer le navigateur
driver.quit()