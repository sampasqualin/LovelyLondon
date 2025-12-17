-- Migration: Adicionar suporte a vídeos na tabela hero_slides
-- Data: 2025-11-13
-- Descrição: Adiciona campos para permitir vídeos como fundo do hero

ALTER TABLE hero_slides ADD COLUMN background_type VARCHAR(50) DEFAULT 'image' COMMENT 'Tipo de fundo: image ou video';
ALTER TABLE hero_slides ADD COLUMN background_video_url TEXT NULLABLE COMMENT 'URL do vídeo (YouTube, Vimeo, etc)';

-- Opcional: Criar índice para melhor performance
CREATE INDEX idx_hero_slides_background_type ON hero_slides(background_type);
