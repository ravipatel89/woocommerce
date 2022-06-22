/**
 * External dependencies
 */
import * as coreParagraph from '@wordpress/block-library/build-module/paragraph';
import {
	getBlockTypes,
	registerBlockType,
	setDefaultBlockName,
} from '@wordpress/blocks';
// import * as coreCode from '@wordpress/block-library/build-module/code';
// import * as coreHeading from '@wordpress/block-library/build-module/heading';
// import * as coreHtml from '@wordpress/block-library/build-module/html';
// import * as coreList from '@wordpress/block-library/build-module/list';
// import * as coreQuote from '@wordpress/block-library/build-module/quote';
// import * as coreSeparator from '@wordpress/block-library/build-module/separator';

console.debug( 'coreParagraph' );
// import { addFilter } from '@wordpress/hooks';

// const registrationFilter = ( settings: BlockInstance, name: string ) => {
// 	switch ( name ) {
// 		case 'core/paragraph':
// 		case 'core/heading':
// 		case 'core/list':
// 			return {
// 				...settings,
// 				attributes: {
// 					...settings.attributes,
// 					placeholder: {
// 						// Don't display a placeholder for new blocks.
// 						default: ' ',
// 					},
// 				},
// 			};

// 		default:
// 			return settings;
// 	}
// };

export const registerBlocks = () => {
	// addFilter(
	// 	'blocks.registerBlockType',
	// 	'dayone/block-registration',
	// 	registrationFilter
	// );
	registerCoreBlocks();
};

// Confirming if blocks are registered is mostly useful for HMR in development,
// but also if there is an unexpected re-run of the registration code in a single
// page load, no warnings will fire.
export const blockIsRegistered = ( blockName ) => {
	const registeredBlocks = getBlockTypes();
	return !! registeredBlocks.find( ( block ) => block.name === blockName );
};

const registerCoreBlocks = () => {
	// Gutenberg's registerCoreBlocks brings in many blocks we don't need.
	// This just registers the minimum blocks we need for Day One.
	[].forEach( ( block ) => {
		if ( ! blockIsRegistered( block.name ) ) {
			registerBlockType(
				{
					name: block.name,
					...block.metadata,
				},
				block.settings
			);
		}
	} );

	setDefaultBlockName( 'blah' );
};
