<?php
/**
 * @file
 * Contains Drupal\intern\Plugin\Block\InternBlock
 */
namespace Drupal\intern\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides an Intern Block
 * @Block(
 *  id="intern_block",
 *  admin_label="Intern Block",
 *  category="Blocks"
 * )
 */
class InternBlock extends BlockBase {
    public function build() {
        return \Drupal::formBuilder()->getForm('Drupal\intern\Form\FirstForm');
    }

    public function blockAccess(AccountInterface $account) {
        /**
         * @var \Drupal\node\Entity\Node $node
         */
        $node = \Drupal::routeMatch()->getParameter('node');
        $nid = $node->nid->value;
        if(is_numeric($nid)){
            return AccessResult::allowedIfHasPermission($account, 'view intern');
        }
        return AccessResult::forbidden();
    }
}