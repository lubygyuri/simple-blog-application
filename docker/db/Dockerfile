FROM postgres:17-bookworm

RUN localedef -i hu_HU -c -f UTF-8 -A /usr/share/locale/locale.alias hu_HU.UTF-8
ENV LANG hu_HU.UTF-8

RUN apt-get update && apt-get install -y --no-install-recommends vim \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
