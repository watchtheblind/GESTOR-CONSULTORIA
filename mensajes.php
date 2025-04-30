<?php
// mensajes.php
?>
<?php
include 'header.php';
include 'sidebar.php';
?>

<main class="content">
  <h1>Contenido Central</h1>
  <section class="logs">
    <h2>Asunto (Título, nombre o asunto por el que se inició la conversación)</h2>
    <div class="chat-container">
      <div class="chat-messages" id="chat-messages">
        <!-- Mensajes aparecerán aquí -->
      </div>
      <div class="chat-input">
        <input type="text" id="message-input" placeholder="Escribe tu mensaje...">
        <button id="send-button">Enviar</button>
      </div>
    </div>
  </section>
</main>
<?php
// Cierra .layout que abrimos en header.php
echo '</div>'; // .layout
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatMessages = document.getElementById('chat-messages');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-button');
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        
        // Cargar mensajes guardados o demo si no hay
        const savedMessages = JSON.parse(localStorage.getItem('chatMessages'));
        if (!savedMessages || savedMessages.length === 0) {
            loadDemoMessages();
        } else {
            loadMessages();
        }
        
        // Enviar mensaje al hacer clic en el botón
        sendButton.addEventListener('click', sendMessage);
        
        // Enviar mensaje al presionar Enter
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
        
        // Toggle del menú en móviles
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
        
        // Cerrar menú al hacer clic en un enlace (en móviles)
        document.querySelectorAll('.nav a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                }
            });
        });
        
        function loadDemoMessages() {
            const demoMessages = [
                {
                    type: 'user',
                    sender: 'Tú',
                    text: 'Hola, tengo un problema con el sistema. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                    timestamp: new Date(Date.now() - 3600000).toISOString()
                },
                {
                    type: 'other',
                    sender: 'Soporte',
                    text: 'Gracias por contactarnos. Hemos recibido tu mensaje. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.',
                    timestamp: new Date(Date.now() - 1800000).toISOString()
                },
                {
                    type: 'user',
                    sender: 'Tú',
                    text: 'El problema persiste cuando intento subir archivos. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                    timestamp: new Date(Date.now() - 900000).toISOString()
                },
                {
                    type: 'other',
                    sender: 'Soporte',
                    text: 'Entendido, estamos investigando el issue. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit.',
                    timestamp: new Date().toISOString()
                }
            ];
            
            // Guardar mensajes demo
            localStorage.setItem('chatMessages', JSON.stringify(demoMessages));
            
            // Cargar mensajes
            loadMessages();
        }
        
        function sendMessage() {
            const messageText = messageInput.value.trim();
            if (messageText === '') return;
            
            // Crear y mostrar mensaje del usuario
            addMessage('user', 'Tú', messageText);
            
            // Guardar mensaje
            saveMessage('user', 'Tú', messageText);
            
            // Limpiar input
            messageInput.value = '';
            
            // Simular respuesta después de un breve retraso
            setTimeout(simulateResponse, 1000);
        }
        
        function simulateResponse() {
            const responses = [
                "Gracias por tu mensaje. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                "Hemos registrado tu consulta. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",
                "Nuestro equipo está trabajando en ello. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur."
            ];
            
            const randomResponse = responses[Math.floor(Math.random() * responses.length)];
            
            // Crear y mostrar mensaje del sistema
            addMessage('other', 'Soporte', randomResponse);
            
            // Guardar mensaje
            saveMessage('other', 'Soporte', randomResponse);
        }
        
        function addMessage(type, sender, text) {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', `message-${type}`);
            
            const messageText = document.createElement('div');
            messageText.textContent = text;
            
            const messageInfo = document.createElement('div');
            messageInfo.classList.add('message-info');
            messageInfo.textContent = `${sender} • ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;
            
            messageDiv.appendChild(messageText);
            messageDiv.appendChild(messageInfo);
            
            chatMessages.appendChild(messageDiv);
            
            // Auto-scroll al último mensaje
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        function saveMessage(type, sender, text) {
            // Obtener mensajes existentes o crear un nuevo array
            let messages = JSON.parse(localStorage.getItem('chatMessages')) || [];
            
            // Añadir nuevo mensaje
            messages.push({
                type,
                sender,
                text,
                timestamp: new Date().toISOString()
            });
            
            // Guardar en localStorage
            localStorage.setItem('chatMessages', JSON.stringify(messages));
        }
        
        function loadMessages() {
            const messages = JSON.parse(localStorage.getItem('chatMessages')) || [];
            
            messages.forEach(msg => {
                // Usar la hora guardada o la actual si no existe
                const time = msg.timestamp ? 
                    new Date(msg.timestamp).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 
                    new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('message', `message-${msg.type}`);
                
                const messageText = document.createElement('div');
                messageText.textContent = msg.text;
                
                const messageInfo = document.createElement('div');
                messageInfo.classList.add('message-info');
                messageInfo.textContent = `${msg.sender} • ${time}`;
                
                messageDiv.appendChild(messageText);
                messageDiv.appendChild(messageInfo);
                
                chatMessages.appendChild(messageDiv);
            });
            
            // Auto-scroll al último mensaje
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });
	document.getElementById('toggle-menu')
        .addEventListener('click',()=>document.querySelector('aside').classList.toggle('open'));

</script>

<?php include 'footer.php';?>