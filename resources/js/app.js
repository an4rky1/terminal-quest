class Terminal {
    constructor() {
        this.logEl = document.getElementById('terminal-log');
        this.inputEl = document.getElementById('terminal-input');
        this.commandHistory = [];
        this.historyIndex = -1;
        this.isTyping = false;

        this.bindEvents();
    }

    bindEvents() {
        this.inputEl.addEventListener('keydown', (e) => this.handleKeydown(e));

        document.addEventListener('click', () => {
            if (!this.isTyping) {
                this.inputEl.focus();
            }
        });
    }

    handleKeydown(e) {
        if (e.key === 'Enter') {
            const command = this.inputEl.value.trim();
            if (command) {
                this.commandHistory.unshift(command);
                this.historyIndex = -1;
                this.submitCommand(command);
            }
            this.inputEl.value = '';
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (this.historyIndex < this.commandHistory.length - 1) {
                this.historyIndex++;
                this.inputEl.value = this.commandHistory[this.historyIndex];
            }
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (this.historyIndex > 0) {
                this.historyIndex--;
                this.inputEl.value = this.commandHistory[this.historyIndex];
            } else {
                this.historyIndex = -1;
                this.inputEl.value = '';
            }
        }
    }

    submitCommand(command) {
        this.printLine(`> ${command}`, 'system');

        if (this.isTyping) {
            setTimeout(() => this.processCommand(command), 100);
        } else {
            this.processCommand(command);
        }
    }

    processCommand(command) {
        const cmd = command.toLowerCase().replace(/^\//, '');
        const parts = cmd.split(' ');
        const action = parts[0];
        const arg = parts.slice(1).join(' ');

        const responses = {
            look: 'Building Entrance\nYou stand at the entrance of an abandoned building. The air smells of dust and old wiring. A flickering light buzzes overhead.\n\nExits: north, east, up\nYou see: Flashlight',
            status: 'Location: Building Entrance\nHealth: 100/100\nExits: north, east, up',
            inventory: 'Your inventory is empty.',
            help: 'Available commands:\n  /go <direction>  - Move (north, south, east, west, up, down)\n  /take <item>     - Pick up an item\n  /use <item>      - Use an item from inventory\n  /status          - Check current status\n  /look            - Look around the room\n  /inventory       - Check your inventory\n  /help            - Show this help',
        };

        if (responses[action]) {
            this.printLine(responses[action]);
        } else if (action === 'go') {
            if (!arg) {
                this.printLine('Go where? Specify a direction: north, south, east, west, up, down.', 'error');
            } else {
                this.printLine(`You move ${arg}.\n\nDark Corridor\nA long corridor stretches before you. Broken fluorescent tubes hang from the ceiling.`, 'system');
            }
        } else {
            this.printLine(`Unknown command: ${action}. Type 'help' for available commands.`, 'error');
        }
    }

    printLine(text, type = '') {
        const entry = document.createElement('div');
        entry.className = `log-entry ${type}`;
        this.logEl.appendChild(entry);

        this.typeText(entry, text);
    }

    typeText(element, text, speed = 8) {
        this.isTyping = true;
        this.inputEl.disabled = true;

        let i = 0;
        const chars = text.split('');

        const type = () => {
            if (i < chars.length) {
                element.textContent += chars[i];
                i++;
                this.logEl.scrollTop = this.logEl.scrollHeight;
                setTimeout(type, speed);
            } else {
                this.isTyping = false;
                this.inputEl.disabled = false;
                this.inputEl.focus();
            }
        };

        type();
    }

    init() {
        const welcome = `╔══════════════════════════════════════╗
║       T E R M I N A L   Q U E S T    ║
║         v1.0 // RETRO EDITION        ║
╚══════════════════════════════════════╝

System initialized. Connection established.
Type 'help' for available commands.
`;
        this.printLine(welcome, 'system');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const terminal = new Terminal();
    terminal.init();
});
