# Cistern level tracker

This project aims to supply a little backend to a sensor in cisterns,
storing fill level data and display a little graph of it.

## Getting started

The container listens on port 8000 for HTTP requests.

To restrict access, create a `.htpasswd` file in the `/app/var` directory. It will
be picked up by the Nginx automatically.

You can run the app using docker by executing
```bash
$ docker run docker.pkg.github.com/soebbing/cistern-level-tracker/image:latest \
     -v ./var:/app/var -p 80:80000
```

## Adding Data

To add data, request the following URL: `/add/<liter float value>`, e.g. `/add/789.5`

The value `789.5` with the unit `Liter` will be stored for the current timestamp automatically.

To provide a custom timestamp, add a second parameter with datetime value `/add/789.5/2020-02-20%2020:02:20`.
