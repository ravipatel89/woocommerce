/**
 * External dependencies
 */

import { Card, CardBody, TabPanel, TextControl } from '@wordpress/components';
import { TextControlWithAffixes, Pill } from '@woocommerce/components';
import { Icon, check } from '@wordpress/icons';
import { useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import './products.scss';
import { ProductsSection } from './index';
import { Editor } from './Editor';

console.debug( 'products screen' );

export const ManageProducts = () => {
	const [ blocks, updateBlocks ] = useState( [] );

	return (
		<>
			<nav>
				<TabPanel
					className="my-tab-panel"
					tabs={ [
						{
							name: 'tab1',
							title: 'Tab 1 title',
							className: 'tab-one',
						},
						{
							name: 'tab2',
							title: 'Tab 2 title',
							className: 'tab-two',
						},
					] }
				>
					{ ( tab ) => <p>Selected tab: { tab.title }</p> }
				</TabPanel>
			</nav>
			<section className="woo-products">
				<ProductsSection
					title="Images"
					description="This is the images description, lorem and ipsum of course"
				>
					<>
						<Card>
							<CardBody>
								Images card form
								<TextControl
									className="card-readers-support-address-line1-input"
									label="Address line 1"
									value={ '' }
									onChange={ ( value ) =>
										console.debug( 'onchange' )
									}
								/>
							</CardBody>
						</Card>
					</>
				</ProductsSection>
				<ProductsSection
					title="Product Details"
					description="This is the product details description, lorem and ipsum of course"
				>
					<>
						<Card>
							<CardBody>
								Product details card form
								<TextControlWithAffixes
									suffix={ <Icon icon={ check } /> }
									className="woocommerce-filters-advanced__input"
									type="text"
									value={ '' }
									onChange={ () =>
										console.debug( 'affixChange' )
									}
								/>
								<Pill>Menswear > Tops, Polos and whatever</Pill>
								<Pill>Menswear > Shirts</Pill>
							</CardBody>
						</Card>
					</>
				</ProductsSection>
				<ProductsSection
					title="Product Description"
					description="Testing Gutenberg editor"
				>
					<Editor blocks={ blocks } />
				</ProductsSection>
			</section>
		</>
	);
};
