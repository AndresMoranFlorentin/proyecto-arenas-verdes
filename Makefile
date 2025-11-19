DIRECTORIO_BASE=/var/www/basedecampamento.loberia.gob.ar
USUARIO_DESPLIEGUE=campamento
USUARIO_WWW=www-data
GRUPO_DESPLIEGUE=www-data
OBJETIVOS=config controllers estilos helpers img js models paginas public \
	  router.php servicios templates vendor views .htaccess \
          cron/script_ejecutar

.PHONY: help install deploy

help :
	@echo "\nmake deploy"
	@echo "  Despliegue puro, debe hacerse desde el usuario $(USUARIO_DESPLIEGUE)"
	@echo "\nmake install"
	@echo "  Instalación completa, debe hacerse desde un usuario"
	@echo "  con privilegios de administrador (sudo)\n"


install :
	sudo install -d -o $(USUARIO_DESPLIEGUE) -g $(GRUPO_DESPLIEGUE) -m 2755 $(DIRECTORIO_BASE)
	sudo rsync -vrR --delete --exclude /config/disponibilidad.txt \
		--exclude "/helpers/tmp/?*" --exclude "/helpers/helpers/" \
		$(OBJETIVOS) $(DIRECTORIO_BASE)
	sudo chown -Rh $(USUARIO_DESPLIEGUE):$(GRUPO_DESPLIEGUE) \
		$(DIRECTORIO_BASE)
	sudo chmod 640 $(DIRECTORIO_BASE)/.htaccess
	sudo chmod 2775 $(DIRECTORIO_BASE)/config $(DIRECTORIO_BASE)/helpers/tmp
	sudo chown $(USUARIO_WWW) $(DIRECTORIO_BASE)/config/disponibilidad.txt
	sudo chown -f $(USUARIO_WWW) $(DIRECTORIO_BASE)/helpers/tmp/* || true

deploy :
	rsync -vrR --delete --exclude config/disponibilidad.txt \
		--exclude "/helpers/tmp/?*" --exclude "/helpers/helpers/" \
		$(OBJETIVOS) $(DIRECTORIO_BASE) \
		$(OBJETIVOS) $(DIRECTORIO_BASE)
