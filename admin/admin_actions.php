<?php
/**
 * =========================================================================
 * ADMIN ACTIONS - CORRIGIDO - Processamento REAL de CRUD
 * =========================================================================
 */

require_once __DIR__ . '/config.php';
requireAuth();

header('Content-Type: application/json');

// Capturar ação
$action = $_REQUEST['_action'] ?? $_REQUEST['action'] ?? null;
$module = $_REQUEST['module'] ?? null;
$id = $_REQUEST['id'] ?? null;

// Validar ação
if (!$action || !$module) {
    echo json_encode(['success' => false, 'message' => 'Ação ou módulo inválido']);
    exit;
}

try {
    
    if ($action === 'save') {
        handleSave($module, $_POST);
    }
    elseif ($action === 'delete') {
        handleDelete($module, $id);
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Ação desconhecida']);
        exit;
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

/**
 * SAVE - Salvar novo ou atualizar existente
 */
function handleSave($module, $post) {
    global $db;
    
    try {
        // Preparar dados
        $data = $post;
        unset($data['_action'], $data['action'], $data['module']);
        
        // Debug backgrounds
        if ($module === 'section_backgrounds') {
            error_log('DEBUG BACKGROUNDS SAVE: ID=' . ($post['id'] ?? 'NEW') . ', DATA=' . json_encode($data));
        }
        
        // Processar uploads de imagem
        $imageFields = getImageFieldsForModule($module);
        foreach ($imageFields as $field) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $uploadResult = handleImageUpload($_FILES[$field], $module);
                if ($uploadResult['success']) {
                    $data[$field] = $uploadResult['path'];
                    // Deletar imagem antiga se houver
                    if (isset($post[$field . '_old']) && !empty($post[$field . '_old'])) {
                        deleteImage($post[$field . '_old']);
                    }
                } else {
                    throw new Exception($uploadResult['message']);
                }
            } elseif (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
                // Manter imagem antiga
                if (isset($post[$field . '_old']) && !empty($post[$field . '_old'])) {
                    $data[$field] = $post[$field . '_old'];
                }
            }
            // Remover campo _old do data (não deve ser salvo no JSON)
            if (isset($data[$field . '_old'])) {
                unset($data[$field . '_old']);
            }
        }
        
        // Processar uploads de vídeo
        $videoFields = getVideoFieldsForModule($module);
        foreach ($videoFields as $field) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $uploadResult = handleVideoUpload($_FILES[$field], $module);
                if ($uploadResult['success']) {
                    $data[$field] = $uploadResult['path'];
                    // Atualizar media_type para video quando um vídeo é enviado
                    if ($module === 'hero_slides') {
                        $data['media_type'] = 'video';
                    }
                    // Deletar vídeo antigo se houver
                    if (isset($post[$field . '_old']) && !empty($post[$field . '_old'])) {
                        deleteVideo($post[$field . '_old']);
                    }
                } else {
                    throw new Exception($uploadResult['message']);
                }
            } elseif (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
                // Manter vídeo antigo
                if (isset($post[$field . '_old']) && !empty($post[$field . '_old'])) {
                    $data[$field] = $post[$field . '_old'];
                }
            }
            // Remover campo _old do data (não deve ser salvo no JSON)
            if (isset($data[$field . '_old'])) {
                unset($data[$field . '_old']);
            }
        }
        
        // Gerar slug se houver título
        if (isset($data['title_pt']) && empty($post['id'])) {
            $data['slug'] = generateSlug($data['title_pt']);
            // Garantir slug único
            $records = $db->query("SELECT * FROM " . $module)->fetchAll(PDO::FETCH_ASSOC);
            $counter = 1;
            $original_slug = $data['slug'];
            while (true) {
                $exists = false;
                foreach ($records as $r) {
                    if ($r['slug'] === $data['slug']) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) break;
                $data['slug'] = $original_slug . '-' . $counter++;
            }
        }
        
        // Limpar dados vazios (mas preservar para backgrounds)
        if ($module !== 'section_backgrounds') {
            foreach ($data as $key => $value) {
                if ($key !== 'id' && ($value === '' || $value === null)) {
                    unset($data[$key]);
                }
            }
        }
        
        // Para backgrounds, garantir que checkbox is_active seja tratado como booleano
        // e que campos numéricos sejam floats
        if ($module === 'section_backgrounds') {
            $data['is_active'] = isset($post['is_active']) ? 1 : 0;

            // Converter campos numéricos para float
            $numericFields = ['image_opacity', 'overlay_opacity'];
            foreach ($numericFields as $field) {
                if (isset($data[$field])) {
                    $data[$field] = (float) $data[$field];
                }
            }
        }

        // Processamento especial para GetYourGuide Widgets
        if ($module === 'getyourguide_widgets') {
            // Tratar is_active
            $data['is_active'] = isset($post['is_active']) ? '1' : '0';

            // Extrair configurações do código HTML do widget
            $widget_code = $post['widget_code'] ?? '';

            // Usar regex para extrair os atributos data-gyg-*
            preg_match('/data-gyg-href="([^"]+)"/', $widget_code, $href_match);
            preg_match('/data-gyg-locale-code="([^"]+)"/', $widget_code, $locale_match);
            preg_match('/data-gyg-widget="([^"]+)"/', $widget_code, $widget_type_match);
            preg_match('/data-gyg-number-of-items="([^"]+)"/', $widget_code, $items_match);
            preg_match('/data-gyg-partner-id="([^"]+)"/', $widget_code, $partner_match);
            preg_match('/data-gyg-tour-ids="([^"]+)"/', $widget_code, $tour_ids_match);

            // Montar widget_config como JSON
            $widget_config = [
                'href' => $href_match[1] ?? 'https://widget.getyourguide.com/default/activities.frame',
                'locale' => $locale_match[1] ?? 'pt-BR',
                'widget_type' => $widget_type_match[1] ?? 'activities',
                'number_of_items' => $items_match[1] ?? '1',
                'partner_id' => $partner_match[1] ?? 'MJKDHZZ',
                'tour_ids' => $tour_ids_match[1] ?? ''
            ];
            $data['widget_config'] = json_encode($widget_config);

            // Salvar o código original também
            $data['widget_code'] = $widget_code;

            // Processar tags: converter de string separada por vírgula para array JSON
            if (isset($post['tags'])) {
                $tags_array = array_map('trim', explode(',', $post['tags']));
                $tags_array = array_filter($tags_array); // Remover vazios
                $data['tags'] = json_encode(array_values($tags_array));
            } else {
                $data['tags'] = json_encode([]);
            }

            // Gerar descrição automática do título
            $data['description'] = $data['title'];

            // Remover campos obsoletos que não são mais usados
            unset($data['price_from'], $data['duration']);
        }
        
        $id = $post['id'] ?? null;
        
        // SALVAR/ATUALIZAR
        if ($id) {
            // UPDATE
            $records = $db->query("SELECT * FROM " . $module)->fetchAll(PDO::FETCH_ASSOC);
            $updated = false;
            
            foreach ($records as $key => $record) {
                if ($record['id'] == $id) {
                    // Manter created_at original
                    if (isset($record['created_at'])) {
                        $data['created_at'] = $record['created_at'];
                    }
                    $data['updated_at'] = date('Y-m-d H:i:s');
                    
                    // Para backgrounds, garantir que todos os campos sejam preservados
                    if ($module === 'section_backgrounds') {
                        // Preserve todos os campos originais
                        foreach ($record as $key_orig => $value_orig) {
                            if (!isset($data[$key_orig])) {
                                $data[$key_orig] = $value_orig;
                            }
                        }
                    }

                    // Para widgets GetYourGuide, limpar campos obsoletos do registro antigo
                    if ($module === 'getyourguide_widgets') {
                        // Remover campos obsoletos do registro antigo antes do merge
                        unset($record['price_from'], $record['duration']);
                    }

                    $records[$key] = array_merge($record, $data);
                    $updated = true;
                    break;
                }
            }
            
            if (!$updated) {
                throw new Exception('Registro não encontrado');
            }
            
            saveToJSON($module, $records);
            
            // Regenerar CSS se for backgrounds
            if ($module === 'section_backgrounds') {
                regenerateBackgroundsCSS();
            }
            
            // Mensagem de sucesso por redirect
            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => ucfirst($module) . ' atualizado com sucesso!'
            ];
            $redirectModule = ($module === 'section_backgrounds') ? 'backgrounds' : $module;
            header('Location: ' . BASE_URL . '/admin/index.php?action=' . $redirectModule);
            
        } else {
            // INSERT
            $data['id'] = generateId();
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $records = $db->query("SELECT * FROM " . $module)->fetchAll(PDO::FETCH_ASSOC);
            $records[] = $data;
            
            saveToJSON($module, $records);
            
            // Regenerar CSS se for backgrounds
            if ($module === 'section_backgrounds') {
                regenerateBackgroundsCSS();
            }
            
            // Mensagem de sucesso por redirect
            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => ucfirst($module) . ' criado com sucesso!'
            ];
            $redirectModule = ($module === 'section_backgrounds') ? 'backgrounds' : $module;
            header('Location: ' . BASE_URL . '/admin/index.php?action=' . $redirectModule);
        }
        
        exit;
        
    } catch (Exception $e) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => $e->getMessage()
        ];
        $redirectModule = ($module === 'section_backgrounds') ? 'backgrounds' : $module;
        header('Location: ' . BASE_URL . '/admin/index.php?action=' . $redirectModule);
        exit;
    }
}

/**
 * DELETE - Deletar registro
 */
function handleDelete($module, $id) {
    global $db;
    
    try {
        if (empty($id)) {
            throw new Exception('ID inválido');
        }
        
        // Carregar registros
        $records = $db->query("SELECT * FROM " . $module)->fetchAll(PDO::FETCH_ASSOC);
        $record = null;
        $record_key = null;
        
        foreach ($records as $key => $r) {
            if ($r['id'] == $id) {
                $record = $r;
                $record_key = $key;
                break;
            }
        }
        
        if ($record === null) {
            throw new Exception('Registro não encontrado');
        }
        
        // Deletar imagens associadas
        $imageFields = getImageFieldsForModule($module);
        foreach ($imageFields as $field) {
            if (isset($record[$field]) && !empty($record[$field])) {
                deleteImage($record[$field]);
            }
        }
        
        // Deletar vídeos associados
        $videoFields = getVideoFieldsForModule($module);
        foreach ($videoFields as $field) {
            if (isset($record[$field]) && !empty($record[$field])) {
                deleteVideo($record[$field]);
            }
        }
        
        // Remover do array
        unset($records[$record_key]);
        
        // Reindexar array
        $records = array_values($records);
        
        // Salvar JSON
        saveToJSON($module, $records);
        
        // Mensagem de sucesso por redirect
        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => ucfirst($module) . ' deletado com sucesso!'
        ];
        $redirectModule = ($module === 'section_backgrounds') ? 'backgrounds' : $module;
        header('Location: ' . BASE_URL . '/admin/index.php?action=' . $redirectModule);
        exit;
        
    } catch (Exception $e) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => $e->getMessage()
        ];
        $redirectModule = ($module === 'section_backgrounds') ? 'backgrounds' : $module;
        header('Location: ' . BASE_URL . '/admin/index.php?action=' . $redirectModule);
        exit;
    }
}

/**
 * Salvar array em arquivo JSON
 */
function saveToJSON($table, $records) {
    $filePath = DATA_PATH . '/' . $table . '.json';
    $json = json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if (file_put_contents($filePath, $json) === false) {
        throw new Exception('Erro ao salvar arquivo JSON');
    }
}

/**
 * Obter campos de imagem para um módulo
 */
function getImageFieldsForModule($module) {
    $imageFields = [
        'tours' => ['image'],
        'services' => ['image_url'],
        'blog_posts' => ['featured_image'],
        'hero_slides' => ['background_image'],
        'testimonials' => ['client_photo'],
        'gallery_photos' => ['photo_url'],
        'clients' => ['logo_url'],
        'section_backgrounds' => ['background_image', 'custom_logo'],
    ];
    
    return $imageFields[$module] ?? [];
}

/**
 * Obter campos de vídeo para um módulo
 */
function getVideoFieldsForModule($module) {
    $videoFields = [
        'hero_slides' => ['background_video'],
    ];
    
    return $videoFields[$module] ?? [];
}

/**
 * Processar upload de imagem
 */
function handleImageUpload($file, $module) {
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowedExtensions)) {
        return ['success' => false, 'message' => 'Tipo de arquivo não permitido'];
    }
    
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'message' => 'Arquivo muito grande (máximo 5MB)'];
    }
    
    // Criar diretório
    $uploadDir = UPLOADS_PATH . '/' . $module . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Gerar nome único
    $filename = 'img_' . uniqid() . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Mover arquivo
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'message' => 'Erro ao salvar arquivo'];
    }
    
    return [
        'success' => true,
        'path' => '/assets/uploads/' . $module . '/' . $filename
    ];
}

/**
 * Processar upload de vídeo
 */
function handleVideoUpload($file, $module) {
    $allowedExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowedExtensions)) {
        return ['success' => false, 'message' => 'Tipo de arquivo não permitido. Use MP4, WebM, OGG, MOV, AVI ou MKV'];
    }
    
    // Verificar tamanho máximo (50MB)
    $maxVideoSize = 50 * 1024 * 1024;
    if ($file['size'] > $maxVideoSize) {
        return ['success' => false, 'message' => 'Arquivo muito grande (máximo 50MB)'];
    }
    
    // Para hero_slides, usar diretório 'hero'
    $uploadSubdir = ($module === 'hero_slides') ? 'hero' : $module;
    
    // Criar diretório
    $uploadDir = UPLOADS_PATH . '/' . $uploadSubdir . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Gerar nome único
    $filename = 'video_' . uniqid() . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Mover arquivo
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'message' => 'Erro ao salvar arquivo de vídeo'];
    }
    
    return [
        'success' => true,
        'path' => '/assets/uploads/' . $uploadSubdir . '/' . $filename
    ];
}

/**
 * Regenerar CSS para backgrounds das seções
 */
function regenerateBackgroundsCSS() {
    $projectRoot = PROJECT_ROOT;
    $dataPath = DATA_PATH;
    
    // Carregar JSON de section_backgrounds
    $jsonFile = $dataPath . '/section_backgrounds.json';
    $sections = [];
    
    if (file_exists($jsonFile)) {
        $json = file_get_contents($jsonFile);
        $sections = json_decode($json, true) ?? [];
    }
    
    // Gerar CSS
    $css = "/**\r\n";
    $css .= " * =========================================================================\r\n";
    $css .= " * LOVELY LONDON - BACKGROUNDS DAS SEÇÕES (GERADO AUTOMATICAMENTE)\r\n";
    $css .= " * =========================================================================\r\n";
    $css .= " * Arquivo gerado automaticamente pelo admin.\r\n";
    $css .= " * Não edite manualmente - suas alterações serão sobrescritas!\r\n";
    $css .= " * Última atualização: " . date('d/m/Y H:i:s') . "\r\n";
    $css .= " * =========================================================================\r\n";
    $css .= " */\r\n\r\n";
    
    // Mapa de seções para seletores CSS
    $selectorMap = [
        'header' => [
            'html body header.header.section-header',
            'html body header.section-header#header-section',
            'html body .header.section-header',
            'html body .header.section-header .header-main',
            'html body .header-main.bg-pattern'
        ],
        'hero' => [
            'html body section.section.section-hero',
            'html body section.section-hero#hero-section',
            'html body .section.section-hero',
            'html body #hero-section.section'
        ],
        'tours' => [
            'html body section.section.section-tours',
            'html body section.section-tours#tours-section',
            'html body .section.section-tours',
            'html body #tours-section.section'
        ],
        'about' => [
            'html body section.section.section-about',
            'html body section.section-about#about-section',
            'html body .section.section-about',
            'html body #about-section.section'
        ],
        'services' => [
            'html body section.section.section-services',
            'html body section.section-services#services-section',
            'html body .section.section-services',
            'html body #services-section.section'
        ],
        'testimonials' => [
            'html body section.section.section-testimonials',
            'html body section.section-testimonials#testimonials-section',
            'html body .section.section-testimonials',
            'html body #testimonials-section.section'
        ],
        'gallery' => [
            'html body section.section.section-gallery',
            'html body section.section-gallery#gallery-section',
            'html body .section.section-gallery',
            'html body #gallery-section.section'
        ],
        'blog' => [
            'html body section.section.section-blog',
            'html body section.section-blog#blog-section',
            'html body .section.section-blog',
            'html body #blog-section.section'
        ],
        'faq' => [
            'html body section.section.section-faq',
            'html body section.section-faq#faq-section',
            'html body .section.section-faq',
            'html body #faq-section.section'
        ],
        'contact' => [
            'html body section.section.section-contact',
            'html body section.section-contact#contact-section',
            'html body .section.section-contact',
            'html body #contact-section.section'
        ],
        'footer' => [
            'html body footer.footer.section-footer',
            'html body footer.section-footer#footer-section',
            'html body .footer.section-footer'
        ],
        'services_cta' => [
            'html body section.section.section-services-cta',
            'html body section.section-services-cta#services-cta-section',
            'html body .section.section-services-cta',
            'html body #services-cta-section.section',
            'html body section.contact-section'
        ]
    ];
    
    // Processar cada seção
    foreach ($sections as $section) {
        if (!$section['is_active']) {
            continue;
        }
        
        $sectionName = $section['section_name'];
        $sectionLabel = $section['section_label'];
        $selectors = $selectorMap[$sectionName] ?? [];
        
        if (empty($selectors)) {
            continue;
        }
        
        $css .= "/* Seção: " . $sectionLabel . " */\r\n";
        $css .= implode(",\r\n", $selectors) . " {\r\n";
        
        // Aplicar background
        $bgType = $section['background_type'] ?? 'color';
        
        if ($bgType === 'color') {
            $css .= "    background-color: " . $section['background_color'] . " !important;\r\n";
        } elseif ($bgType === 'image') {
            if (!empty($section['background_image'])) {
                $css .= "    background-image: url('" . $section['background_image'] . "') !important;\r\n";
                $css .= "    background-size: cover !important;\r\n";
                $css .= "    background-position: center !important;\r\n";
                $css .= "    background-repeat: no-repeat !important;\r\n";
            }
        } elseif ($bgType === 'gradient') {
            $css .= "    background: linear-gradient(" . $section['gradient_direction'] . ", " . $section['gradient_start'] . ", " . $section['gradient_end'] . ") !important;\r\n";
        } elseif ($bgType === 'both') {
            if (!empty($section['background_image'])) {
                $css .= "    background-image: url('" . $section['background_image'] . "') !important;\r\n";
                $css .= "    background-size: cover !important;\r\n";
                $css .= "    background-position: center !important;\r\n";
                $css .= "    background-repeat: no-repeat !important;\r\n";
                $css .= "    background-blend-mode: multiply !important;\r\n";
            }
            $css .= "    background-color: " . $section['background_color'] . " !important;\r\n";
        }
        
        // Text color
        if (!empty($section['text_color'])) {
            $css .= "    color: " . $section['text_color'] . " !important;\r\n";
        }
        
        $css .= "}\r\n";
        
        // Aplicar overlay no ::before para header
        if ($sectionName === 'header') {
            $css .= "html body .header-main::before {\r\n";
            $css .= "    display: none !important;\r\n";
            $css .= "    content: none !important;\r\n";
            $css .= "}\r\n";
        }
        
        $css .= "\r\n";
    }
    
    // Salvar CSS
    $cssPath = $projectRoot . '/assets/css/section-backgrounds.css';
    file_put_contents($cssPath, $css);
}
?>
