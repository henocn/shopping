<?php

namespace src;

use PDO;
use Exception;

/**
 * Classe pour la gestion des produits et du stock.
 * Basée strictement sur les tables: products, product_stock, product_current_costs
 */
class ProductManager
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère les produits avec un stock bas.
     */
    public function getLowStockAlerts($limit = 10)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    p.id,
                    p.name,
                    ps.quantity,
                    ps.low_stock_threshold
                FROM product_stock ps
                JOIN products p ON ps.product_id = p.id
                WHERE ps.quantity <= ps.low_stock_threshold
                ORDER BY ps.quantity ASC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getLowStockAlerts: " . $e->getMessage());
            return [];
        }
    }



    /**
     * Récuperation de la liste des produits vendu (newstat = deliver) avec leur taux de benefice et le benefice total
     * depuis la table orders
     */
    public function getSoldProducts()
    {
        try {
            $stmt = $this->pdo->prepare("
            WITH product_expenses AS (
                SELECT 
                    product_id,
                    COALESCE(SUM(cout), 0) as total_expenses
                FROM depense 
                WHERE type = 'products'
                GROUP BY product_id
            )
            SELECT 
                p.id,
                p.name,
                SUM(o.purchase_price * o.quantity) AS cost_price,
                SUM(o.total_price) AS total_selling_price,
                SUM(o.quantity) AS total_sold,
                COALESCE(pe.total_expenses, 0) as total_expenses,
                ((SUM(o.total_price) - (SUM(o.purchase_price * o.quantity) + COALESCE(pe.total_expenses, 0)))/SUM(o.quantity)) AS avg_profit_per_unit,
                (SUM(o.total_price) - (SUM(o.purchase_price * o.quantity) + COALESCE(pe.total_expenses, 0))) AS total_profit,
                ROUND(((SUM(o.total_price) - (SUM(o.purchase_price * o.quantity) + COALESCE(pe.total_expenses, 0))) / SUM(o.purchase_price * o.quantity)) * 100, 2) AS rendement
            FROM orders o
            JOIN products p ON o.product_id = p.id
            LEFT JOIN product_expenses pe ON p.id = pe.product_id
            WHERE o.newstat = 'deliver'
            GROUP BY p.id, p.name, pe.total_expenses
            ORDER BY total_sold DESC
        ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Erreur getSoldProducts: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère le montant total des achats
     */
    public function getTotalPurchaseAmount()
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT SUM(purchase_price * quantity) as total
                FROM orders
                WHERE newstat = 'deliver'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            error_log("Erreur getTotalPurchaseAmount: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupère le montant total des ventes
     */
    public function getTotalSalesAmount()
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT SUM(total_price) as total
                FROM orders
                WHERE newstat = 'deliver'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            error_log("Erreur getTotalSalesAmount: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupère la liste complète des produits pour les sélecteurs.
     */
    public function getAllProducts()
    {
        try {
            $stmt = $this->pdo->query("SELECT id, name FROM products ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Erreur getAllProducts: ' . $e->getMessage());
            return [];
        }
    }
}
