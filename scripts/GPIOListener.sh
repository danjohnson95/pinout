#!/bin/bash

PIPE_PATH="${1}"
GPIO_PINS=(2 3 4 17 27 22 10 9 11 5 6 13 19 26 14 15 18 23 24 25 8 7 12 16 20 21)

# Ensure the named pipe exists
if [[ ! -p "$PIPE_PATH" ]]; then
    mkfifo "$PIPE_PATH"
    chmod 666 "$PIPE_PATH"
fi

echo "Listening for GPIO interrupts on all pins..."

# Function to clean up on exit
cleanup() {
    # echo "Cleaning up GPIO pins..."
    # for PIN in "${GPIO_PINS[@]}"; do
    #     echo "$PIN" > /sys/class/gpio/unexport 2>/dev/null
    # done
    exit 0
}

# Catch termination signals (e.g., when Artisan command is quit)
trap cleanup SIGINT SIGTERM

# Export all GPIO pins and set them to input
for PIN in "${GPIO_PINS[@]}"; do
    if [[ ! -d "/sys/class/gpio/gpio$PIN" ]]; then
        echo "$PIN" > /sys/class/gpio/export 2>/dev/null
    fi
    echo "in" > "/sys/class/gpio/gpio$PIN/direction"
done

# Monitor GPIO pins for changes using an infinite loop
while true; do
    for PIN in "${GPIO_PINS[@]}"; do
        if [[ $(cat /sys/class/gpio/gpio$PIN/value) -eq 0 ]]; then
            echo '{"pin": '"$PIN"', "event": "triggered"}' > "$PIPE_PATH"
            sleep 0.3  # Debounce
        fi
    done
done
