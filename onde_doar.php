<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onde Doar - Encontre e Doe</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="onde_doar.css">
    <link rel="icon" type="image/x-icon" href="img/icon.ico">
</head>
<body>
 
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="img/logo.png" alt="Encontre e Doe Logo" height="50">
            </a>
            <div class="navbar-nav mx-auto">
                <a class="nav-link mx-3" href="index.php">Início</a>
                <a class="nav-link mx-3" href="onde_doar.php">Onde Doar</a>
                <a class="nav-link mx-3" href="sobre_nos.php">Sobre Nós</a>
                <a class="nav-link mx-3" href="contato.php">Contato</a>
            </div>
            <a href="login.php" class="btn btn-success rounded-pill px-4">Entrar</a>
        </div>
    </nav>

    <main>
        <div class="container">
            <div class="search-container">
                <h1 class="mb-3">🔍 Encontre um Local para Doar</h1>
                <p class="mb-4">Para facilitar sua busca, utilize nosso sistema de localização!</p>
                
                <div class="search-instructions">
                    <p>📍 Digite seu CEP para encontrar os pontos de doação mais próximos.</p>
                    <p>🎯 Use os filtros para selecionar o tipo de doação, como roupas, alimentos, móveis, eletrônicos e muito mais.</p>
                    <p>🗺️ Veja no mapa ou na lista os locais que aceitam sua doação e escolha o mais conveniente para você.</p>
                </div>

                <div class="search-box">
                    <div class="input-group mb-3">
                        <input type="text" id="cep" class="form-control" placeholder="Digite seu CEP" maxlength="8">
                        <button class="btn btn-success" type="button" id="buscarCep">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <button class="btn btn-outline-danger" type="button" id="limparBusca">
                            <i class="fas fa-times"></i> Limpar
                        </button>
                    </div>
                </div>

                <div class="filter-container mb-3">
                    <h5 class="mb-2">Filtrar por tipo de doação:</h5>
                    <div class="btn-group flex-wrap" role="group" aria-label="Filtros de doação">
                        <button type="button" class="btn btn-outline-success m-1" data-filter="roupas">
                            👕 Roupas
                        </button>
                        <button type="button" class="btn btn-outline-success m-1" data-filter="alimentos">
                            🥫 Alimentos
                        </button>
                        <button type="button" class="btn btn-outline-success m-1" data-filter="moveis">
                            🪑 Móveis
                        </button>
                        <button type="button" class="btn btn-outline-success m-1" data-filter="eletronicos">
                            📱 Eletrônicos
                        </button>
                        <button type="button" class="btn btn-outline-success m-1" data-filter="todos">
                            🔄 Mostrar Todos
                        </button>
                    </div>
                </div>

                <div class="results-container">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="results-list" id="placesList">
                               
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div id="map"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        let map;
        let markers = [];
        let todosOsLocais = [];
        let filtroAtual = 'todos';

        
        function initMap() {
            try {
                map = L.map('map').setView([-23.550520, -46.633308], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
                console.log('Mapa inicializado com sucesso');
            } catch (error) {
                console.error('Erro ao inicializar o mapa:', error);
                document.getElementById('map').innerHTML = 
                    '<div class="alert alert-danger">Erro ao carregar o mapa. Por favor, tente recarregar a página.</div>';
            }
        }

    
        async function buscarCEP() {
            const cep = document.getElementById('cep').value.replace(/\D/g, '');
            
            if (cep.length !== 8) {
                alert('Digite um CEP válido com 8 números');
                return;
            }

            document.getElementById('placesList').innerHTML = 
                '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Buscando locais...</div>';

            try {
                console.log('Buscando CEP:', cep);
                const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                if (!response.ok) {
                    throw new Error('Erro ao buscar CEP');
                }
                const data = await response.json();
                console.log('Dados do CEP:', data);

                if (!data.erro) {
                    const nominatimUrl = `https://nominatim.openstreetmap.org/search?street=${data.logradouro}&city=${data.localidade}&state=${data.uf}&country=Brazil&format=json`;
                    console.log('Buscando coordenadas:', nominatimUrl);
                    
                    const locationResponse = await fetch(nominatimUrl);
                    if (!locationResponse.ok) {
                        throw new Error('Erro ao buscar coordenadas');
                    }
                    const locations = await locationResponse.json();
                    console.log('Dados de localização:', locations);

                    if (locations && locations.length > 0) {
                        const lat = parseFloat(locations[0].lat);
                        const lon = parseFloat(locations[0].lon);
                        
                        map.setView([lat, lon], 15);
                        
                        
                        markers.forEach(marker => map.removeLayer(marker));
                        markers = [];

                        
                        const marker = L.marker([lat, lon]).addTo(map);
                        markers.push(marker);

                        
                        const overpassUrl = `https://overpass-api.de/api/interpreter?data=[out:json];(node["amenity"="place_of_worship"](around:5000,${lat},${lon});node["amenity"="community_centre"](around:5000,${lat},${lon});node["amenity"="social_centre"](around:5000,${lat},${lon});node["office"="ngo"](around:5000,${lat},${lon});node["office"="association"](around:5000,${lat},${lon});node["social_facility"](around:5000,${lat},${lon});node["social_facility"="food_bank"](around:5000,${lat},${lon});node["shop"="second_hand"](around:5000,${lat},${lon});node["shop"="charity"](around:5000,${lat},${lon});node["organisation"="charity"](around:5000,${lat},${lon});node["amenity"="social_facility"](around:5000,${lat},${lon});way["amenity"="place_of_worship"](around:5000,${lat},${lon});way["amenity"="community_centre"](around:5000,${lat},${lon});way["amenity"="social_centre"](around:5000,${lat},${lon});way["social_facility"](around:5000,${lat},${lon}););out body;>;out skel qt;`;
                        
                        const overpassResponse = await fetch(overpassUrl);
                        const overpassData = await overpassResponse.json();

                        mostrarResultados(overpassData.elements, [lat, lon]);
                    } else {
                        throw new Error('Localização não encontrada');
                    }
                } else {
                    throw new Error('CEP não encontrado');
                }
            } catch (error) {
                document.getElementById('placesList').innerHTML = 
                    `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${error.message}</div>`;
            }
        }

        function mostrarResultados(places, center) {
            
            todosOsLocais = places.filter(place => 
                place.tags && 
                place.tags.name && 
                typeof place.lat === 'number' && 
                typeof place.lon === 'number'
            );

            aplicarFiltro(filtroAtual);
        }

        function aplicarFiltro(tipo) {
            filtroAtual = tipo;
            const placesList = document.getElementById('placesList');
            placesList.innerHTML = '';

            
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            let locaisFiltrados = todosOsLocais;

            if (tipo !== 'todos') {
                locaisFiltrados = todosOsLocais.filter(place => {
                    const tags = place.tags;
                    switch(tipo) {
                        case 'roupas':
                            return tags.accepts_clothes === 'yes' || 
                                   tags.shop === 'second_hand' || 
                                   tags.shop === 'charity' ||
                                   tags.amenity === 'social_centre' ||
                                   tags.social_facility === 'clothing';
                        case 'alimentos':
                            return tags.social_facility === 'food_bank' ||
                                   tags.amenity === 'social_centre' ||
                                   tags.food_bank === 'yes';
                        case 'moveis':
                            return tags.accepts_furniture === 'yes' ||
                                   tags.shop === 'second_hand' ||
                                   tags.amenity === 'social_centre';
                        case 'eletronicos':
                            return tags.accepts_electronics === 'yes' ||
                                   tags.shop === 'second_hand' ||
                                   tags.recycling_type === 'electronics';
                        default:
                            return true;
                    }
                });
            }

            if (locaisFiltrados.length === 0) {
                placesList.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Nenhum local encontrado para doação de ${tipo}. Tente outro tipo de doação ou outro CEP.
                    </div>`;
                return;
            }

            
            locaisFiltrados.forEach(place => {
                
                let tipo = 'Local de doação';
                let icone = '📍';

                if (place.tags.amenity === 'place_of_worship') {
                    tipo = 'Igreja/Centro Religioso';
                    icone = '⛪';
                } else if (place.tags.social_facility) {
                    tipo = 'Centro de Assistência Social';
                    icone = '🏢';
                } else if (place.tags.office === 'association') {
                    tipo = 'Associação Beneficente';
                    icone = '🏛️';
                } else if (place.tags.office === 'ngo') {
                    tipo = 'ONG';
                    icone = '🤝';
                } else if (place.tags.shop === 'second_hand' || place.tags.shop === 'charity') {
                    tipo = 'Brechó/Bazar Beneficente';
                    icone = '🏪';
                } else if (place.tags.amenity === 'social_centre') {
                    tipo = 'Centro Social';
                    icone = '🏢';
                } else if (place.tags.amenity === 'community_centre') {
                    tipo = 'Centro Comunitário';
                    icone = '🏛️';
                }

                try {
                    
                    const marker = L.marker([place.lat, place.lon]).addTo(map);
                    markers.push(marker);

                    
                    let infoAdicional = '';
                    if (place.tags.phone) infoAdicional += `<p>📞 ${place.tags.phone}</p>`;
                    if (place.tags.website) infoAdicional += `<p>🌐 <a href="${place.tags.website}" target="_blank">Website</a></p>`;
                    if (place.tags.opening_hours) infoAdicional += `<p>⏰ ${place.tags.opening_hours}</p>`;
                    if (place.tags.address) infoAdicional += `<p>📍 ${place.tags.address}</p>`;

                    
                    function formatarEndereco(place, data) {
                        let endereco = place.tags.name;
                        
                        
                        if (place.tags['addr:street']) {
                            endereco += `, ${place.tags['addr:street']}`;
                            if (place.tags['addr:housenumber']) {
                                endereco += `, ${place.tags['addr:housenumber']}`;
                            }
                        }
                        
                       
                        if (place.tags['addr:city']) {
                            endereco += `, ${place.tags['addr:city']}`;
                        }
                        if (place.tags['addr:state']) {
                            endereco += `, ${place.tags['addr:state']}`;
                        }

                        return encodeURIComponent(endereco);
                    }

                    
                    const enderecoFormatado = formatarEndereco(place);
                    const googleMapsUrl = `https://www.google.com/maps/search/${enderecoFormatado}`;

                  
                    marker.bindPopup(`
                        <div>
                            <h5>${icone} ${place.tags.name}</h5>
                            <p><strong>${tipo}</strong></p>
                            ${infoAdicional}
                            <div class="mt-2">
                                <a href="${googleMapsUrl}" 
                                   target="_blank" class="btn btn-sm btn-success">
                                    <i class="fas fa-map-marker-alt"></i> Ver no Google Maps
                                </a>
                                <a href="https://www.google.com/maps/dir//${enderecoFormatado}" 
                                   target="_blank" class="btn btn-sm btn-outline-success ms-1">
                                    <i class="fas fa-route"></i> Como chegar
                                </a>
                            </div>
                        </div>
                    `);

                    
                    const placeItem = document.createElement('div');
                    placeItem.className = 'place-item';
                    placeItem.innerHTML = `
                        <h5>${icone} ${place.tags.name}</h5>
                        <p class="tipo-local"><strong>${tipo}</strong></p>
                        ${infoAdicional}
                        <div class="mt-2">
                            <a href="${googleMapsUrl}" 
                               target="_blank" class="btn btn-sm btn-success">
                                <i class="fas fa-map-marker-alt"></i> Ver no Google Maps
                            </a>
                            <a href="https://www.google.com/maps/dir//${enderecoFormatado}" 
                               target="_blank" class="btn btn-sm btn-outline-success ms-1">
                                <i class="fas fa-route"></i> Como chegar
                            </a>
                        </div>
                    `;
                    placesList.appendChild(placeItem);
                } catch (error) {
                    console.error('Erro ao adicionar local:', error);
                }
            });

          
            const resultadosCount = document.createElement('div');
            resultadosCount.className = 'alert alert-success mb-3';
            resultadosCount.innerHTML = `
                <i class="fas fa-check-circle"></i> 
                Encontrados ${locaisFiltrados.length} locais de doação 
                ${tipo !== 'todos' ? `para ${tipo}` : ''} próximos`;
            placesList.insertBefore(resultadosCount, placesList.firstChild);

            
            if (markers.length > 0) {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }

        
        document.addEventListener('DOMContentLoaded', initMap);

        
        document.getElementById('buscarCep').addEventListener('click', buscarCEP);
        document.getElementById('cep').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                buscarCEP();
            }
        });

        
        document.addEventListener('DOMContentLoaded', function() {
            const botoesFilto = document.querySelectorAll('[data-filter]');
            botoesFilto.forEach(botao => {
                botao.addEventListener('click', function() {
                    
                    botoesFilto.forEach(b => b.classList.remove('active'));
                    
                    this.classList.add('active');
                    
                    aplicarFiltro(this.dataset.filter);
                });
            });
        });

        function limparBusca() {
            
            document.getElementById('cep').value = '';
            
            
            document.getElementById('placesList').innerHTML = '';
            
            
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];
            
            
            todosOsLocais = [];
            
            
            document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
            
            
            filtroAtual = 'todos';
            
            
            map.setView([-23.550520, -46.633308], 13);
        }

        
        document.getElementById('limparBusca').addEventListener('click', limparBusca);
    </script>
</body>
</html>
