sync:
  rsync -avz --exclude 'vendor/' ./ pi@192.168.1.132:pinout
