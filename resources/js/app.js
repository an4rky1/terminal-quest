class Terminal {
    constructor() {
        this.logEl = document.getElementById('terminal-log');
        this.inputEl = document.getElementById('terminal-input');
        this.commandHistory = [];
        this.historyIndex = -1;
        this.isTyping = false;
        this.playerId = localStorage.getItem('player_id') || null;

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
        this.sendToApi(command);
    }

    async sendToApi(command) {
        this.inputEl.disabled = true;

        try {
            const response = await fetch('/api/game/command', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    command: command,
                    player_id: this.playerId ? parseInt(this.playerId) : null,
                }),
            });

            const data = await response.json();

            if (data.player_id) {
                this.playerId = data.player_id;
                localStorage.setItem('player_id', this.playerId);
            }

            if (data.success) {
                this.printLine(data.message);
            } else {
                this.printLine(data.message, 'error');
            }
        } catch (error) {
            this.printLine('Connection lost. The mainframe is unreachable.', 'error');
        }

        this.inputEl.disabled = false;
        this.inputEl.focus();
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
        const boot = `  TERMINAL QUEST
  ──────────────

  KERNEL  :: v3.14.159
  MEMORY  :: 640K OK
  NETWORK :: CONNECTED
  USER    :: ANONYMOUS

  > Establishing secure connection... [DONE]
  > Loading game engine...            [DONE]
  > Initializing world state...       [DONE]

  ─────────────────────────────────────

  Welcome, hacker. Type 'help' for available commands.`;
        this.printLine(boot, 'system');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const terminal = new Terminal();
    terminal.init();
});
