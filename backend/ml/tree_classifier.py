import torch
import torchvision.transforms as transforms
from torchvision import models
import torch.nn as nn
from PIL import Image
import io
import json
import sys
import base64

class TreeClassifier:
    def __init__(self, model_path):
        self.device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
        
        # Load the model architecture
        self.model = models.resnet18(pretrained=False)
        self.model.fc = nn.Linear(self.model.fc.in_features, 2)
        
        # Load the trained weights
        self.model.load_state_dict(torch.load(model_path, map_location=self.device))
        self.model.to(self.device)
        self.model.eval()
        
        # Define the same transforms used during training
        self.transform = transforms.Compose([
            transforms.Resize((224, 224)),
            transforms.ToTensor(),
            transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225])
        ])
        
        self.classes = ['not_tropical_tree', 'tropical_tree']

    def preprocess_image(self, image_bytes):
        # Convert bytes to PIL Image
        image = Image.open(io.BytesIO(image_bytes)).convert('RGB')
        return self.transform(image).unsqueeze(0)

    def predict(self, image_bytes):
        try:
            # Preprocess the image
            input_tensor = self.preprocess_image(image_bytes)
            input_tensor = input_tensor.to(self.device)
            
            # Make prediction
            with torch.no_grad():
                output = self.model(input_tensor)
                probabilities = torch.nn.functional.softmax(output[0], dim=0)
                predicted_class = torch.argmax(probabilities).item()
                confidence = probabilities[predicted_class].item()
                
            return {
                'success': True,
                'class': self.classes[predicted_class],
                'confidence': round(confidence * 100, 2),
                'is_tropical_tree': predicted_class == 1
            }
        except Exception as e:
            return {
                'success': False,
                'error': str(e)
            }

def main():
    # Get base64 encoded image from command line argument
    if len(sys.argv) != 2:
        print(json.dumps({'success': False, 'error': 'Invalid arguments'}))
        return

    try:
        # Decode base64 image
        image_bytes = base64.b64decode(sys.argv[1])
        
        # Initialize classifier
        classifier = TreeClassifier('tropical_tree_classifier.pth')
        
        # Make prediction
        result = classifier.predict(image_bytes)
        
        # Return JSON result
        print(json.dumps(result))
        
    except Exception as e:
        print(json.dumps({'success': False, 'error': str(e)}))

if __name__ == "__main__":
    main()
